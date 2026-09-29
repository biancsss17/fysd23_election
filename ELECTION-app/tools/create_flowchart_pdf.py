from reportlab.lib.pagesizes import A4, landscape
from reportlab.pdfgen import canvas
from reportlab.lib import colors
from reportlab.lib.units import mm
from reportlab.pdfbase.pdfmetrics import stringWidth
from pathlib import Path

OUT = Path(__file__).resolve().parents[1] / "output" / "pdf" / "admin-user-flowchart.pdf"
OUT.parent.mkdir(parents=True, exist_ok=True)

W, H = landscape(A4)
NAVY = colors.HexColor("#0b1b30")
BLUE = colors.HexColor("#1d63c6")
PALE_BLUE = colors.HexColor("#eaf1ff")
PURPLE = colors.HexColor("#6735dc")
PALE_PURPLE = colors.HexColor("#f0eaff")
GOLD = colors.HexColor("#f4c34e")
PALE_GOLD = colors.HexColor("#fff5d6")
INK = colors.HexColor("#12233d")
MUTED = colors.HexColor("#52637b")
GREEN = colors.HexColor("#278653")
PALE_GREEN = colors.HexColor("#e7f6ed")

def rounded_box(c, x, y, w, h, fill, stroke=colors.white, radius=7, title=None, body=None):
    c.setFillColor(fill)
    c.setStrokeColor(stroke)
    c.setLineWidth(1)
    c.roundRect(x, y, w, h, radius, fill=1, stroke=1)
    if title:
        c.setFillColor(INK)
        c.setFont("Helvetica-Bold", 9.2 if len(title) > 20 else 11)
        c.drawString(x + 10, y + h - 18, title)
    if body:
        c.setFillColor(MUTED)
        c.setFont("Helvetica", 8.5)
        lines = body if isinstance(body, list) else [body]
        for i, line in enumerate(lines):
            c.drawString(x + 10, y + h - 32 - i * 11, line)

def arrow(c, x1, y1, x2, y2, color=BLUE):
    c.setStrokeColor(color)
    c.setFillColor(color)
    c.setLineWidth(1.7)
    c.line(x1, y1, x2, y2)
    import math
    angle = math.atan2(y2 - y1, x2 - x1)
    size = 6
    p1 = (x2 - size * math.cos(angle - 0.5), y2 - size * math.sin(angle - 0.5))
    p2 = (x2 - size * math.cos(angle + 0.5), y2 - size * math.sin(angle + 0.5))
    c.line(x2, y2, *p1)
    c.line(x2, y2, *p2)

def header(c, section, subtitle):
    c.setFillColor(NAVY)
    c.rect(0, H - 30 * mm, W, 30 * mm, fill=1, stroke=0)
    c.setFillColor(GOLD)
    c.setFont("Helvetica-Bold", 9)
    c.drawString(16 * mm, H - 12 * mm, "DISTRICT 23 FYS - ELECTION OF OFFICERS FOR 2027-2030")
    c.setFillColor(colors.white)
    c.setFont("Helvetica-Bold", 20)
    c.drawString(16 * mm, H - 22 * mm, section)
    c.setFont("Helvetica", 8.5)
    c.drawRightString(W - 16 * mm, H - 20 * mm, subtitle)

def footer(c, page):
    c.setStrokeColor(colors.HexColor("#dbe3ef"))
    c.line(16 * mm, 12 * mm, W - 16 * mm, 12 * mm)
    c.setFillColor(MUTED)
    c.setFont("Helvetica", 8)
    c.drawString(16 * mm, 7 * mm, "System flow chart")
    c.drawRightString(W - 16 * mm, 7 * mm, f"Page {page}")

def page_user(c):
    header(c, "User Pages", "Voter journey")
    y_top = H - 52 * mm
    c.setFillColor(INK)
    c.setFont("Helvetica-Bold", 13)
    c.drawString(16 * mm, y_top, "User voting flow")
    c.setFillColor(MUTED)
    c.setFont("Helvetica", 9)
    c.drawString(16 * mm, y_top - 7 * mm, "The user follows the current election position until the ballot is submitted.")

    x0, bw, bh, gap = 18 * mm, 42 * mm, 23 * mm, 13 * mm
    y1 = 115 * mm
    xs = [x0 + i * (bw + gap) for i in range(5)]
    labels = [
        ("Splash page", ["Continue to election"]),
        ("Overview", ["Current position", "and election status"]),
        ("Candidacy / nomination", ["Submit candidate", "or nominate a peer"]),
        ("Voter access", ["Open current ballot"]),
        ("Ballot marking", ["Choose candidate", "or abstain"]),
    ]
    fills = [PALE_BLUE, PALE_BLUE, PALE_GOLD, PALE_PURPLE, PALE_BLUE]
    for x, data, fill in zip(xs, labels, fills):
        rounded_box(c, x, y1, bw, bh, fill, colors.HexColor("#c8d7ef"), title=data[0], body=data[1])
    for i in range(4):
        arrow(c, xs[i] + bw, y1 + bh / 2, xs[i + 1], y1 + bh / 2)

    y2 = 65 * mm
    labels2 = [
        ("Locked ballot", ["Wait for admin", "unlock or close"]),
        ("Review vote", ["Check selections"]),
        ("Verify and submit", ["Confirm final vote"]),
        ("Receipt", ["Vote recorded"]),
        ("Next position / results", ["Continue or view"]),
    ]
    xs2 = [x0 + i * (bw + gap) for i in range(5)]
    for x, data in zip(xs2, labels2):
        rounded_box(c, x, y2, bw, bh, PALE_GREEN if data[0] != "Locked ballot" else colors.HexColor("#fff0f0"), colors.HexColor("#c8d7ef"), title=data[0], body=data[1])
    for i in range(4):
        arrow(c, xs2[i] + bw, y2 + bh / 2, xs2[i + 1], y2 + bh / 2)
    arrow(c, xs[-1] + bw / 2, y1, xs2[0] + bw / 2, y2 + bh, color=PURPLE)
    c.setFillColor(PURPLE)
    c.setFont("Helvetica-Bold", 8)
    c.drawCentredString(xs[-1] + bw / 2, (y1 + y2 + bh) / 2, "if ballot is locked")

    c.setFillColor(INK)
    c.setFont("Helvetica-Bold", 11)
    c.drawString(18 * mm, 37 * mm, "End states")
    c.setFillColor(MUTED)
    c.setFont("Helvetica", 9)
    c.drawString(18 * mm, 30 * mm, "No positions: candidacy, nomination, and voting remain locked.")
    c.drawString(18 * mm, 24 * mm, "No vote and no abstention: no winner is shown. Multi-seat positions only fill seats with eligible winners.")
    footer(c, 1)

def page_admin(c):
    header(c, "Admin Pages", "Election control and records")
    c.setFillColor(INK)
    c.setFont("Helvetica-Bold", 13)
    c.drawString(16 * mm, H - 52 * mm, "Admin control flow")
    c.setFillColor(MUTED)
    c.setFont("Helvetica", 9)
    c.drawString(16 * mm, H - 59 * mm, "The admin controls positions, voting access, results, printing, reset, and recovery.")

    x, y, w, h = 18 * mm, 125 * mm, 47 * mm, 24 * mm
    rounded_box(c, x, y, w, h, PALE_BLUE, colors.HexColor("#c8d7ef"), title="Admin login", body=["Authenticate"])
    arrow(c, x+w, y+h/2, x+w+12*mm, y+h/2)
    x2 = x+w+12*mm
    rounded_box(c, x2, y, w, h, PALE_BLUE, colors.HexColor("#c8d7ef"), title="Dashboard", body=["Overview and controls"])
    arrow(c, x2+w, y+h/2, x2+w+12*mm, y+h/2)
    x3 = x2+w+12*mm
    rounded_box(c, x3, y, w, h, PALE_GOLD, colors.HexColor("#e6cc78"), title="Position management", body=["Create, configure, delete"])

    yb = 83 * mm
    cols = [
        ("Voter management", ["Manage voters"]),
        ("Candidates & nominations", ["Approve, reject, edit"]),
        ("Ballot controls", ["Unlock or close voting"]),
        ("Live results", ["Counts and winners"]),
        ("Results preview", ["Edit names and print"]),
    ]
    bw, gap = 38 * mm, 7 * mm
    start = 18 * mm
    boxes = []
    for i, (title, body) in enumerate(cols):
        bx = start + i * (bw + gap)
        rounded_box(c, bx, yb, bw, 22 * mm, PALE_PURPLE if i >= 3 else PALE_BLUE, colors.HexColor("#c8d7ef"), title=title, body=body)
        boxes.append((bx, yb))
        arrow(c, x2 + w/2, y, bx + bw/2, yb + 22 * mm, color=BLUE)

    y3 = 42 * mm
    rounded_box(c, 18 * mm, y3, 62 * mm, 23 * mm, colors.HexColor("#fff0f0"), colors.HexColor("#e8b7b7"), title="Reset election", body=["Archive current results", "and clear live data"])
    rounded_box(c, 92 * mm, y3, 62 * mm, 23 * mm, PALE_GOLD, colors.HexColor("#e6cc78"), title="Audit log", body=["Stored reset data", "and archive names"])
    rounded_box(c, 166 * mm, y3, 62 * mm, 23 * mm, PALE_GREEN, colors.HexColor("#b9ddc7"), title="Restore election", body=["Recover one stored", "reset archive"])
    arrow(c, boxes[4][0] + bw/2, yb, 49 * mm, y3 + 23 * mm, color=PURPLE)
    arrow(c, 80 * mm, y3 + 11.5 * mm, 92 * mm, y3 + 11.5 * mm, color=PURPLE)
    arrow(c, 154 * mm, y3 + 11.5 * mm, 166 * mm, y3 + 11.5 * mm, color=GREEN)
    c.setFillColor(MUTED)
    c.setFont("Helvetica", 8.5)
    c.drawString(18 * mm, 25 * mm, "Reset updates the user Overview immediately. Restore repopulates the election data and user Overview.")
    c.drawString(18 * mm, 19 * mm, "Live results are read-only. Final document supports editing winner names and printing the official result.")
    footer(c, 2)

c = canvas.Canvas(str(OUT), pagesize=landscape(A4))
c.setTitle("District 23 FYS Admin and User Page Flow Chart")
page_user(c)
c.showPage()
page_admin(c)
c.save()
print(OUT)
