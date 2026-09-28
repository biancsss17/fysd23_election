from pathlib import Path
from reportlab.lib import colors
from reportlab.lib.enums import TA_CENTER
from reportlab.lib.pagesizes import A4
from reportlab.lib.styles import getSampleStyleSheet, ParagraphStyle
from reportlab.lib.units import mm
from reportlab.platypus import BaseDocTemplate, PageTemplate, Frame, Paragraph, Spacer, Image, PageBreak, Table, TableStyle, KeepTogether
from reportlab.lib.utils import ImageReader
from PIL import Image as PILImage, ImageDraw, ImageFont

ROOT = Path(r"C:\Users\User\Documents\ChatGPT\ELECTION")
OUT = ROOT / "ELECTION-app" / "output" / "pdf"
OUT.mkdir(parents=True, exist_ok=True)
PDF = OUT / "district-23-fys-user-admin-brochure.pdf"
ASSET = Path(r"C:\Users\User\AppData\Local\Temp")
MOCK = ROOT / "ELECTION-app" / "tmp" / "brochure-clean"
MOCK.mkdir(parents=True, exist_ok=True)
LIVE = ROOT / "ELECTION-app" / "tmp" / "brochure-clean-live"
def crop_live(name, source, top, bottom):
    path = LIVE / name
    with PILImage.open(LIVE / source) as image:
        image.crop((0, top, image.width, min(bottom, image.height))).save(path)
    return path

IMG = {
    "splash": LIVE / "splash.png",
    "overview": LIVE / "overview.png",
    "voter_access": LIVE / "voter_access.png",
    "locked": LIVE / "voter_access.png",
    "results": LIVE / "results.png",
    "admin": crop_live("admin-dashboard-controls.png", "admin_dashboard.png", 0, 930),
    "voters": crop_live("admin-voters-roster.png", "admin_voters.png", 0, 820),
    "positions": crop_live("admin-positions-sequence.png", "admin_positions.png", 0, 900),
    "candidates": LIVE / "admin_candidates.png",
    "admin_results": crop_live("admin-results-live.png", "admin_results.png", 0, 980),
    "document": LIVE / "document.png",
    "audit": LIVE / "admin_audit.png",
}
NAVY = colors.HexColor("#0b192c")
BLUE = colors.HexColor("#1765c1")
PALE = colors.HexColor("#eef2ff")
TEXT = colors.HexColor("#26364d")
MUTED = colors.HexColor("#65748b")

def make_clean_mockup(filename, title, subtitle, card_title, card_text, accent="#1765c1"):
    path = MOCK / filename
    canvas = PILImage.new("RGB", (1200, 650), "#f7f8fc")
    draw = ImageDraw.Draw(canvas)
    try:
        bold = ImageFont.truetype(r"C:\Windows\Fonts\segoeuib.ttf", 30)
        regular = ImageFont.truetype(r"C:\Windows\Fonts\segoeui.ttf", 20)
        small = ImageFont.truetype(r"C:\Windows\Fonts\segoeui.ttf", 16)
    except OSError:
        bold = regular = small = ImageFont.load_default()
    draw.rectangle((0, 0, 1200, 90), fill="#0b192c")
    draw.ellipse((28, 24, 64, 60), fill="#f4bd3c")
    draw.text((82, 24), "DISTRICT 23 FYS - ELECTION OF OFFICERS FOR 2027-2030", fill="white", font=small)
    draw.text((82, 48), title, fill="white", font=bold)
    draw.text((48, 130), subtitle, fill="#1765c1", font=small)
    draw.text((48, 160), card_title, fill="#0b192c", font=bold)
    draw.rounded_rectangle((48, 220, 1152, 500), radius=18, fill="white", outline="#dbe2ef", width=2)
    draw.text((90, 275), card_text, fill="#65748b", font=regular)
    draw.rounded_rectangle((90, 370, 1110, 430), radius=12, fill=accent)
    draw.text((450, 388), "NO SAMPLE DATA", fill="white", font=small)
    canvas.save(path)
    return path

def make_splash():
    path = MOCK / "clean-splash.png"
    canvas = PILImage.new("RGB", (1200, 520), "#0b192c")
    draw = ImageDraw.Draw(canvas)
    try:
        bold = ImageFont.truetype(r"C:\Windows\Fonts\segoeuib.ttf", 42)
        regular = ImageFont.truetype(r"C:\Windows\Fonts\segoeui.ttf", 22)
    except OSError:
        bold = regular = ImageFont.load_default()
    draw.ellipse((525, 55, 675, 205), fill="#f4bd3c", outline="white", width=5)
    draw.ellipse((550, 80, 650, 180), fill="#1765c1", outline="white", width=3)
    draw.text((300, 260), "DISTRICT 23 FYS", fill="white", font=bold)
    draw.text((290, 325), "Election of Officers for 2027-2030", fill="#dbe7ff", font=regular)
    draw.text((445, 405), "OFFICIAL ELECTION PORTAL", fill="#f4bd3c", font=regular)
    canvas.save(path)
    return path

IMG.update({
    "clean_overview": make_clean_mockup("clean-overview.png", "Overview", "CURRENT ELECTION", "No election position created yet", "Create the first position to begin the election."),
    "clean_admin": make_clean_mockup("clean-admin.png", "Administrator Portal", "ELECTION OVERVIEW", "No active election position", "The administrator dashboard is ready for setup."),
    "clean_positions": make_clean_mockup("clean-positions.png", "Position Management", "BALLOT SEQUENCE", "No ballot sequence yet", "Create a position to begin building the sequence."),
    "clean_results": make_clean_mockup("clean-results.png", "Results and Document Preview", "LIVE RESULT", "No results available", "Results will appear after ballots are submitted."),
    "clean_document": make_clean_mockup("clean-document.png", "Official Document", "FINAL DOCUMENT", "No winners recorded yet", "The printable table will populate after voting."),
    "clean_audit": make_clean_mockup("clean-audit.png", "Audit Log", "STORED ELECTION RECORDS", "No stored election records yet", "Reset an election to create an archive."),
    "clean_locked": make_clean_mockup("clean-locked.png", "Ballot Marking", "BALLOT ACCESS", "Ballot locked", "Please wait for the administrator to unlock this position.", accent="#65748b"),
})

styles = getSampleStyleSheet()
styles.add(ParagraphStyle("CoverTitle", parent=styles["Title"], fontName="Helvetica-Bold", fontSize=27, leading=32, textColor=colors.white, alignment=TA_CENTER, spaceAfter=8))
styles.add(ParagraphStyle("CoverSub", parent=styles["Normal"], fontSize=13, leading=18, textColor=colors.HexColor("#dbe7ff"), alignment=TA_CENTER))
styles.add(ParagraphStyle("Section", parent=styles["Heading1"], fontName="Helvetica-Bold", fontSize=22, leading=26, textColor=NAVY, spaceAfter=8))
styles.add(ParagraphStyle("Kicker", parent=styles["Normal"], fontName="Helvetica-Bold", fontSize=9, leading=12, textColor=BLUE, spaceAfter=4))
styles.add(ParagraphStyle("StepTitle", parent=styles["Heading2"], fontName="Helvetica-Bold", fontSize=15, leading=19, textColor=NAVY, spaceAfter=4))
styles.add(ParagraphStyle("Body2", parent=styles["BodyText"], fontSize=10.2, leading=14.5, textColor=TEXT, spaceAfter=6))
styles.add(ParagraphStyle("Small", parent=styles["BodyText"], fontSize=8.5, leading=11, textColor=MUTED))
styles.add(ParagraphStyle("ScenarioCell", parent=styles["BodyText"], fontSize=7.2, leading=8.6, textColor=TEXT, spaceAfter=0))
styles.add(ParagraphStyle("ScenarioHead", parent=styles["BodyText"], fontName="Helvetica-Bold", fontSize=7.4, leading=8.8, textColor=colors.white, spaceAfter=0))
styles.add(ParagraphStyle("Callout", parent=styles["BodyText"], fontName="Helvetica-Bold", fontSize=10, leading=14, textColor=NAVY, backColor=PALE, borderColor=colors.HexColor("#d6e1ff"), borderWidth=0.6, borderPadding=8, spaceBefore=5, spaceAfter=8))
styles.add(ParagraphStyle("Footer", parent=styles["Normal"], fontSize=8, textColor=colors.HexColor("#7a879a"), alignment=TA_CENTER))

def page_header(canvas, doc):
    canvas.saveState()
    w, h = A4
    if doc.page > 1:
        canvas.setFillColor(NAVY)
        canvas.rect(0, h - 13 * mm, w, 13 * mm, fill=1, stroke=0)
        canvas.setFillColor(colors.white)
        canvas.setFont("Helvetica-Bold", 8.5)
        canvas.drawString(18 * mm, h - 8.3 * mm, "DISTRICT 23 FYS - ELECTION OF OFFICERS FOR 2027-2030")
        canvas.setFillColor(colors.HexColor("#718099"))
        canvas.setFont("Helvetica", 8)
        canvas.drawCentredString(w / 2, 10 * mm, f"User and Administrator Guide  |  Page {doc.page}")
    canvas.restoreState()

def screenshot(key, width=166 * mm, height=78 * mm):
    path = IMG[key]
    if not path.exists():
        return Paragraph(f"Screenshot unavailable: {path.name}", styles["Small"])
    iw, ih = ImageReader(str(path)).getSize()
    scale = min(width / iw, height / ih)
    return Image(str(path), width=iw * scale, height=ih * scale, hAlign="CENTER")

def step(n, title, text, image_key=None, image_height=78 * mm):
    result = [Paragraph(f"STEP {n}", styles["Kicker"]), Paragraph(title, styles["StepTitle"]), Paragraph(text, styles["Body2"])]
    if image_key:
        result += [screenshot(image_key, height=image_height), Spacer(1, 3 * mm)]
    return KeepTogether(result)

doc = BaseDocTemplate(str(PDF), pagesize=A4, leftMargin=17 * mm, rightMargin=17 * mm, topMargin=19 * mm, bottomMargin=17 * mm, title="District 23 FYS User and Administrator Brochure")
doc.addPageTemplates([PageTemplate(id="main", frames=[Frame(doc.leftMargin, doc.bottomMargin, doc.width, doc.height, id="normal")], onPage=page_header)])
story = []

story += [Spacer(1, 8 * mm)]
splash_img = screenshot("splash", width=166 * mm, height=63 * mm)
cover = Table([[splash_img], [Spacer(1, 3 * mm)], [Paragraph("PRINTABLE USER AND ADMINISTRATOR BROCHURE", styles["CoverSub"])]], colWidths=[doc.width])
cover.setStyle(TableStyle([("BACKGROUND", (0, 0), (-1, -1), NAVY), ("LEFTPADDING", (0, 0), (-1, -1), 17 * mm), ("RIGHTPADDING", (0, 0), (-1, -1), 17 * mm), ("TOPPADDING", (0, 0), (-1, -1), 9 * mm), ("BOTTOMPADDING", (0, 0), (-1, -1), 9 * mm)]))
story += [cover, Spacer(1, 12 * mm), Paragraph("A visual guide for voters, election administrators, results handling, audit records, and final document printing.", styles["Body2"]), Paragraph("This brochure follows the actual election flow: open a position, submit candidacy or nomination, vote, close the ballot, review results, and print the official document.", styles["Callout"]), Spacer(1, 6 * mm), Paragraph("Quick contents", styles["Section"])]
contents = Table([["PART 1", "User / voter process"], ["PART 2", "Administrator process"], ["PART 3", "Results, audit log, and recovery"], ["PART 4", "Operating notes"], ["PART 5", "Scenario checklist"]], colWidths=[28 * mm, 120 * mm])
contents.setStyle(TableStyle([("BACKGROUND", (0, 0), (-1, -1), colors.HexColor("#f6f8fd")), ("TEXTCOLOR", (0, 0), (0, -1), BLUE), ("FONTNAME", (0, 0), (0, -1), "Helvetica-Bold"), ("GRID", (0, 0), (-1, -1), 0.4, colors.HexColor("#dbe2ef")), ("FONTSIZE", (0, 0), (-1, -1), 10), ("TOPPADDING", (0, 0), (-1, -1), 7), ("BOTTOMPADDING", (0, 0), (-1, -1), 7)]))
story.append(contents)

story += [PageBreak(), Paragraph("PART 1", styles["Kicker"]), Paragraph("User / voter process", styles["Section"]), Paragraph("Voters use the public pages to view the current election position, submit candidacy or nomination, verify their registered email, cast a ballot, and view the final result.", styles["Body2"]), step("1", "Open the election overview", "The public Overview page shows the current position, stage, timeline, and available actions. Only the current position is shown to users.", "overview", 72 * mm), PageBreak(), step("2", "File candidacy or nominate a peer", "Choose File candidacy to submit yourself, or Submit nomination to nominate another registered voter. Enter the name and registered email, then submit the form.", "overview", 66 * mm), Paragraph("Use the exact email address registered by the administrator. An unregistered address is rejected.", styles["Callout"]), step("3", "Verify voter access", "Open Voter Access and enter the registered email address. A valid email opens the ballot for the current position.", "voter_access", 70 * mm), PageBreak(), step("4", "Mark the ballot", "Select a candidate, or select Abstain when you do not want to choose a candidate. In a multi-seat position, select up to the permitted maximum.", "results", 80 * mm), step("5", "Review and submit the vote", "Review the selected option, enter the same registered email, choose Verify email, and then choose Submit final vote.", "locked", 62 * mm), PageBreak(), step("6", "Wait for the result", "After submission, the result is temporarily hidden while the ballot is finalized. Voters who never submit are automatically counted as abstentions when the ballot closes.", "locked", 70 * mm), step("7", "View the result", "The results page lists winners by seat, vote counts, and abstentions. If there are more seats than winning candidates, the remaining seats stay without a winner.", "results", 82 * mm)]

story += [PageBreak(), Paragraph("PART 2", styles["Kicker"]), Paragraph("Administrator process", styles["Section"]), Paragraph("Administrators control the election sequence, voter roster, candidate approvals, ballot access, closing, results, and recovery records.", styles["Body2"]), step("1", "Sign in to the administrator portal", "Use the administrator account to access the control dashboard. The navigation includes Dashboard, Voter Management, Position Management, Results and Document Preview, and Audit Log.", "admin", 82 * mm), PageBreak(), step("2", "Manage the voter roster", "Import a roster using one email per line, add individual voters, or change a voter's eligibility. The dashboard counts eligible voters, completed ballots, remaining voters, and participation.", "voters", 92 * mm), step("3", "Create the ballot sequence", "Create positions in order. Configure the name, number of seats, single-seat or multi-seat rule, Abstain availability, and maximum selections.", "positions", 92 * mm), PageBreak(), step("4", "Review candidates and nominations", "The Candidates and Nominations page displays real submissions with the candidate name, email, submission type, status, and Approve or Reject controls. Approve submissions, then finalize the roster.", "candidates", 90 * mm), step("5", "Unlock and close voting", "Unlock the current ballot when it is ready. Voting can close automatically when the countdown ends or manually from the admin dashboard. Closing records automatic abstentions for eligible voters who did not submit.", "admin", 80 * mm), PageBreak(), step("6", "Read live results", "The admin dashboard shows only the current position while it is active. It displays the current winner, ballot count, candidate totals, abstentions, and participation.", "admin_results", 92 * mm), step("7", "Edit and print the official document", "Use Results and Document Preview to edit winner names directly in the document table. Multi-seat winners are shown as numbered entries. Print after all positions are complete.", "document", 84 * mm)]

story += [PageBreak(), Paragraph("PART 3", styles["Kicker"]), Paragraph("Results, audit log, and recovery", styles["Section"]), step("8", "Reset and archive the election", "Before resetting, enter a descriptive archive name. Reset removes active election data but stores the results and snapshot in the Audit Log. Only the administrator account is preserved.", "audit", 70 * mm), step("9", "Open stored reset data", "Audit Log shows one stored record for the reset archive. Open it to view archived results by ballot position, including seats, winners, vote totals, and abstentions.", "audit", 70 * mm), step("10", "Restore a previous election", "Open a stored record and choose Restore this election. Restoration replaces the current election data with the archived snapshot.", "audit", 70 * mm), Paragraph("Confirm the archive name before resetting. Restore is a replacement operation and should be used only when the correct stored election has been selected.", styles["Callout"])]

story += [PageBreak(), Paragraph("PART 4", styles["Kicker"]), Paragraph("Operating notes", styles["Section"])]
notes = Table([["Current position", "The public user page displays only the active position, not the whole election at once."], ["Yet to cast", "Remaining equals eligible voters minus voters with a recorded vote or automatic abstention."], ["No winner", "If no candidate receives a vote, the position has no winner. Seats without winners remain unfilled."], ["Multi-seat", "The results document numbers each elected seat and can leave later seats blank."], ["Printing", "Use Results and Document Preview after editing names and checking the table."], ["Audit log", "Reset archives are for recovery and official historical storage. Avoid duplicate empty archives."]], colWidths=[39 * mm, 112 * mm])
notes.setStyle(TableStyle([("BACKGROUND", (0, 0), (0, -1), PALE), ("TEXTCOLOR", (0, 0), (0, -1), NAVY), ("FONTNAME", (0, 0), (0, -1), "Helvetica-Bold"), ("GRID", (0, 0), (-1, -1), 0.4, colors.HexColor("#dbe2ef")), ("VALIGN", (0, 0), (-1, -1), "TOP"), ("FONTSIZE", (0, 0), (-1, -1), 9.5), ("TOPPADDING", (0, 0), (-1, -1), 8), ("BOTTOMPADDING", (0, 0), (-1, -1), 8)]))
story += [notes, Spacer(1, 10 * mm), Paragraph("District 23 FYS Election of Officers for 2027-2030", styles["Footer"]), Paragraph("Prepared as a printable operational guide for local election use.", styles["Footer"])]

story += [PageBreak(), Paragraph("PART 5", styles["Kicker"]), Paragraph("Scenario checklist", styles["Section"]), Paragraph("Use these clean, data-free scenarios to verify the election flow during setup or acceptance testing. The examples describe expected behavior without adding sample records to the live election.", styles["Body2"])]
scenarios = [
    ["1", "Fresh installation", "No positions exist", "Overview shows the original setup state; candidacy, nomination, and voting remain locked until a position is created."],
    ["2", "Single-seat vote", "One position with one seat", "One candidate can win; if no valid vote is recorded, the result shows no winner."],
    ["3", "Multi-seat partial vote", "Two seats, only one valid vote", "Only one seat receives a winner; the second seat remains without a winner."],
    ["4", "Multi-seat full vote", "More than one seat and enough selections", "Winners are displayed in numbered seat order and the printable document uses the same order."],
    ["5", "Automatic abstention", "An eligible voter does not submit", "When voting closes, the voter is counted as an abstention and Yet to cast reaches the correct value."],
    ["6", "Manual close", "Administrator closes voting early", "The ballot closes immediately, blocks new votes, and preserves the recorded totals."],
    ["7", "Countdown close", "The ballot timer reaches zero", "The system closes voting automatically and applies abstentions to eligible non-voters."],
    ["8", "Reset and archive", "Administrator resets with an archive name", "Active data is cleared, one named audit record is stored, and the user Overview returns to its original state."],
    ["9", "Restore archive", "Administrator restores a stored record", "The archived positions and results return to the active election and the user Overview updates without a manual refresh."],
    ["10", "Print final document", "All positions are complete", "The final document shows the current winners, numbered multi-seat entries, and table colors in print preview."],
]
scenario_rows = [[Paragraph(value, styles["ScenarioHead"]) for value in ["#", "Scenario", "Setup", "Expected result"]]]
scenario_rows += [[Paragraph(value, styles["ScenarioCell"]) for value in row] for row in scenarios]
scenario_table = Table(scenario_rows, colWidths=[9 * mm, 34 * mm, 48 * mm, 60 * mm], repeatRows=1)
scenario_table.setStyle(TableStyle([("BACKGROUND", (0, 0), (-1, 0), NAVY), ("TEXTCOLOR", (0, 0), (-1, 0), colors.white), ("FONTNAME", (0, 0), (-1, 0), "Helvetica-Bold"), ("FONTSIZE", (0, 0), (-1, -1), 7.7), ("LEADING", (0, 0), (-1, -1), 9.5), ("GRID", (0, 0), (-1, -1), 0.35, colors.HexColor("#dbe2ef")), ("VALIGN", (0, 0), (-1, -1), "TOP"), ("BACKGROUND", (0, 1), (-1, -1), colors.HexColor("#f7f9fd")), ("BACKGROUND", (0, 2), (-1, 2), colors.white), ("BACKGROUND", (0, 4), (-1, 4), colors.white), ("BACKGROUND", (0, 6), (-1, 6), colors.white), ("BACKGROUND", (0, 8), (-1, 8), colors.white), ("TOPPADDING", (0, 0), (-1, -1), 5), ("BOTTOMPADDING", (0, 0), (-1, -1), 5)]))
story += [scenario_table, Spacer(1, 8 * mm), Paragraph("Testing note: run each scenario in a disposable local test election, then reset and remove the archive after verification. Do not use production voter records for scenario testing.", styles["Callout"])]
doc.build(story)
print(PDF)
