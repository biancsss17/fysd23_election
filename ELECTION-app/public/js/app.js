document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.auto-dismiss-5s').forEach((alert) => {
        window.setTimeout(() => {
            alert.style.transition = 'opacity 300ms ease, transform 300ms ease';
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-4px)';
            window.setTimeout(() => alert.remove(), 320);
        }, 5000);
    });

    const closeModal = (modal) => {
        if (!modal) return;
        modal.classList.add('opacity-0');
        window.setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 200);
    };

    const showStageClosedError = (modal, message) => {
        if (!modal) return;
        let error = modal.querySelector('.modal-live-error');
        if (!error) {
            error = document.createElement('div');
            error.className = 'modal-live-error mx-4 mt-4 rounded-lg border border-red-200 bg-red-50 p-3 text-sm font-semibold text-red-700';
            modal.querySelector('.modal-form')?.before(error);
        }
        error.textContent = message;
        modal.querySelectorAll('.modal-form input, .modal-form button[type="submit"]').forEach((element) => { element.disabled = true; });
    };

    document.querySelectorAll('.modal-open').forEach((button) => {
        button.addEventListener('click', () => {
            const modal = document.getElementById(button.dataset.modal);
            if (!modal) return;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            requestAnimationFrame(() => modal.classList.remove('opacity-0'));
        });
    });

    document.querySelectorAll('.modal').forEach((modal) => {
        modal.addEventListener('click', (event) => {
            if (event.target === modal) closeModal(modal);
        });
        modal.querySelectorAll('.modal-close').forEach((button) => {
            button.addEventListener('click', () => closeModal(modal));
        });
        modal.querySelector('.modal-form')?.addEventListener('submit', (event) => {
            if (modal.querySelector('.modal-form')?.dataset.serverForm) return;
            event.preventDefault();
            modal.querySelector('.modal-form').classList.add('hidden');
            modal.querySelector('.modal-success')?.classList.remove('hidden');
        });
    });

    const ballotInputs = document.querySelectorAll('input[name="presidential_ballot"]');
    const submitBallotButton = document.getElementById('submit-ballot-btn');
    let ballotCountdownTimer = null;
    const startBallotCountdown = (seconds) => {
        const counter = document.getElementById('ballot-countdown');
        if (!counter) return;
        document.getElementById('ballot-countdown-wrap')?.classList.remove('hidden');
        window.clearInterval(ballotCountdownTimer);
        let remaining = Math.min(60, Math.max(0, Math.floor(Number(seconds) || 0)));
        counter.textContent = String(remaining);
        const expireBallot = async () => {
            try {
                await fetch(window.ballotCloseUrl, { method: 'POST', headers: { 'X-CSRF-TOKEN': window.voteCsrfToken || '', Accept: 'application/json' }, credentials: 'same-origin' });
            } finally {
                window.location.replace(window.resultsUrl || '/results');
            }
        };
        if (remaining <= 0) {
            expireBallot();
            return;
        }
        ballotCountdownTimer = window.setInterval(() => {
            remaining -= 1;
            counter.textContent = String(Math.max(remaining, 0));
            if (remaining <= 0) {
                window.clearInterval(ballotCountdownTimer);
                expireBallot();
            }
        }, 1000);
    };
    if (window.ballotRemainingSeconds > 0) startBallotCountdown(window.ballotRemainingSeconds);

    const updateBallotSelection = () => {
        let selected = false;

        ballotInputs.forEach((input) => {
            const card = input.closest('.candidate-option');
            const dot = card?.querySelector('.dot');
            const indicator = card?.querySelector('.radio-indicator');
            const isChecked = input.checked;

            selected ||= isChecked;
            dot?.classList.toggle('scale-100', isChecked);
            dot?.classList.toggle('scale-0', !isChecked);
            indicator?.classList.toggle('border-[#0b192c]', isChecked);
            indicator?.classList.toggle('bg-[#0b192c]', isChecked);
        });

        submitBallotButton?.toggleAttribute('disabled', !selected);
    };

    ballotInputs.forEach((input) => input.addEventListener('change', updateBallotSelection));
    submitBallotButton?.addEventListener('click', () => {
        if (!document.querySelector('input[name="presidential_ballot"]:checked')) return;
        submitBallotButton.innerHTML = '<span class="material-symbols-outlined animate-spin text-xl">refresh</span><span>Preparing Verification...</span>';
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = window.voteSubmitUrl || submitBallotButton.dataset.nextUrl;
        const token = document.createElement('input');
        token.type = 'hidden';
        token.name = '_token';
        token.value = window.voteCsrfToken || '';
        form.appendChild(token);
        if (window.electionPosition?.id) {
            const position = document.createElement('input');
            position.type = 'hidden';
            position.name = 'position_id';
            position.value = window.electionPosition.id;
            form.appendChild(position);
        }
        document.querySelectorAll('input[name="presidential_ballot"]:checked').forEach((input) => {
            const choice = document.createElement('input');
            choice.type = 'hidden';
            choice.name = 'choices[]';
            choice.value = input.value;
            form.appendChild(choice);
        });
        document.body.appendChild(form);
        form.submit();
    });

    const boardInputs = Array.from(document.querySelectorAll('.candidate-checkbox'));
    const abstainInput = document.getElementById('abstain-checkbox');
    const trackerCounter = document.getElementById('tracker-counter');
    const limitBanner = document.getElementById('limit-banner');
    const seatsTrack = document.getElementById('seats-visual-track');
    const reviewButton = document.getElementById('review-cta');
    const maxSeats = Number(window.electionPosition?.max_selections || window.electionPosition?.seats || 4);

    const updateBoardSelection = () => {
        const isAbstaining = Boolean(abstainInput?.checked);
        const selectedCount = boardInputs.filter((input) => input.checked).length;

        if (trackerCounter) trackerCounter.textContent = isAbstaining ? `ABSTAINED (0 / ${maxSeats})` : `SELECTED: ${selectedCount} / ${maxSeats}`;
        Array.from(seatsTrack?.children || []).forEach((bar, index) => {
            bar.classList.toggle('bg-secondary', !isAbstaining && index < selectedCount);
            bar.classList.toggle('bg-surface-container-high', isAbstaining || index >= selectedCount);
        });

        boardInputs.forEach((input) => {
            const card = input.closest('.candidate-card');
            const visual = card?.querySelector('.checkbox-visual');
            const icon = visual?.querySelector('.material-symbols-outlined');
            const isChecked = input.checked;
            card?.classList.toggle('bg-surface-container', isChecked);
            card?.classList.toggle('bg-white', !isChecked);
            visual?.classList.toggle('bg-secondary', isChecked);
            visual?.classList.toggle('bg-surface-container-high', !isChecked);
            icon?.classList.toggle('opacity-100', isChecked);
            icon?.classList.toggle('opacity-0', !isChecked);
        });

        const abstainCard = document.getElementById('abstain-card');
        const abstainVisual = document.getElementById('abstain-visual');
        const abstainIcon = abstainVisual?.querySelector('.material-symbols-outlined');
        abstainCard?.classList.toggle('bg-surface-container-highest', isAbstaining);
        abstainCard?.classList.toggle('bg-surface-container-low', !isAbstaining);
        abstainVisual?.classList.toggle('bg-secondary', isAbstaining);
        abstainVisual?.classList.toggle('bg-surface-container-high', !isAbstaining);
        abstainIcon?.classList.toggle('opacity-100', isAbstaining);
        abstainIcon?.classList.toggle('opacity-0', !isAbstaining);
        limitBanner?.classList.toggle('hidden', isAbstaining || selectedCount < maxSeats);
    };

    boardInputs.forEach((input) => input.addEventListener('change', () => {
        if (input.checked && abstainInput) abstainInput.checked = false;
        if (boardInputs.filter((candidate) => candidate.checked).length > maxSeats) input.checked = false;
        updateBoardSelection();
    }));
    abstainInput?.addEventListener('change', () => {
        if (abstainInput.checked) boardInputs.forEach((input) => { input.checked = false; });
        updateBoardSelection();
    });
    reviewButton?.addEventListener('click', () => {
        if (!boardInputs.some((input) => input.checked) && !abstainInput?.checked) {
            window.alert(`Please select up to ${maxSeats} candidates or choose ABSTAIN to proceed.`);
            return;
        }
        reviewButton.classList.add('opacity-80');
        reviewButton.innerHTML = '<span class="material-symbols-outlined animate-spin text-xl">progress_activity</span><span>COUNTING VOTE...</span>';
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = window.voteSubmitUrl || reviewButton.dataset.nextUrl;
        const token = document.createElement('input');
        token.type = 'hidden';
        token.name = '_token';
        token.value = window.voteCsrfToken || '';
        form.appendChild(token);
        if (window.electionPosition?.id) {
            const position = document.createElement('input');
            position.type = 'hidden';
            position.name = 'position_id';
            position.value = window.electionPosition.id;
            form.appendChild(position);
        }
        boardInputs.filter((input) => input.checked).forEach((input) => {
            const choice = document.createElement('input');
            choice.type = 'hidden';
            choice.name = 'choices[]';
            choice.value = `candidate-${input.value}`;
            form.appendChild(choice);
        });
        if (abstainInput?.checked) {
            const choice = document.createElement('input');
            choice.type = 'hidden';
            choice.name = 'choices[]';
            choice.value = 'abstain';
            form.appendChild(choice);
        }
        document.body.appendChild(form);
        form.submit();
    });
    if (boardInputs.length) updateBoardSelection();

    const submitFinalVote = document.getElementById('submit-final-vote');
    const submitFinalVoteForm = submitFinalVote?.closest('form');
    submitFinalVoteForm?.addEventListener('submit', (event) => {
        if (submitFinalVote?.dataset.emailVerified !== 'true') {
            event.preventDefault();
            return;
        }
        submitFinalVote.disabled = true;
        submitFinalVote.classList.add('opacity-80');
        submitFinalVote.innerHTML = '<span class="material-symbols-outlined animate-spin text-[22px]">progress_activity</span><span>REDIRECTING TO COUNTDOWN...</span>';
    });

    document.querySelectorAll('.copy-receipt').forEach((button) => {
        button.addEventListener('click', async () => {
            const token = button.dataset.token || '';
            try {
                await navigator.clipboard.writeText(token);
                button.textContent = 'Copied!';
                window.setTimeout(() => { button.textContent = 'Copy token'; }, 2000);
            } catch {
                button.textContent = 'Copy unavailable';
            }
        });
    });

    const passwordInput = document.getElementById('admin-password');
    const passwordToggle = document.getElementById('toggle-pwd-visibility');
    const passwordIcon = document.getElementById('pwd-icon');
    passwordToggle?.addEventListener('click', () => {
        const isPassword = passwordInput?.type === 'password';
        if (passwordInput) passwordInput.type = isPassword ? 'text' : 'password';
        if (passwordIcon) passwordIcon.textContent = isPassword ? 'visibility_off' : 'visibility';
    });

    const position = window.electionPosition;
    const candidateSubmissions = window.candidateSubmissions;
    const escapeHtml = (value) => String(value).replace(/[&<>'"]/g, (character) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;' })[character]);
    if (Array.isArray(candidateSubmissions)) {
        const ballotForm = document.getElementById('ballot-form');
        if (ballotForm) {
            ballotForm.classList.remove('grid', 'gap-3', 'lg:grid-cols-2');
            ballotForm.classList.add('flex', 'flex-col', 'gap-3');
            const abstainCard = Array.from(ballotForm.querySelectorAll('.candidate-option')).find((card) => card.querySelector('input[value="abstain"]'));
            const abstainContainer = abstainCard?.closest('.lg\\:col-span-2') || abstainCard;
            ballotForm.querySelectorAll('.candidate-option').forEach((card) => { if (card !== abstainCard) card.remove(); });
            candidateSubmissions.forEach((candidate, index) => {
                const card = document.createElement('label');
                card.className = 'candidate-option group relative block cursor-pointer transition-all duration-200';
                card.innerHTML = `<input class="peer sr-only" name="presidential_ballot" type="radio" value="candidate-${candidate.id}"><div class="flex items-center justify-between gap-3 rounded-xl border border-slate-200/80 bg-white p-4 shadow-sm transition-all peer-checked:border-[#0b192c] peer-checked:bg-slate-50/60 peer-checked:ring-2 peer-checked:ring-[#0b192c]/10"><div class="flex min-w-0 items-center gap-3"><div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-slate-200 bg-slate-100 text-sm font-bold text-[#0b192c]">${String(index + 1).padStart(2, '0')}</div><span class="truncate font-display text-base font-bold text-[#0b192c]">${escapeHtml(candidate.candidate_name)}</span></div><div class="radio-indicator flex h-6 w-6 shrink-0 items-center justify-center rounded-full border-2 border-slate-300 transition-all"><div class="dot h-2 w-2 scale-0 rounded-full bg-white"></div></div></div></label>`;
                ballotForm.insertBefore(card, abstainContainer || null);
            });

            if (candidateSubmissions.length === 0) {
                const emptyState = document.createElement('p');
                emptyState.className = 'lg:col-span-2 rounded-xl border border-dashed border-slate-300 bg-white p-6 text-center text-sm text-slate-500';
                emptyState.textContent = 'No candidates are currently listed for this ballot.';
                ballotForm.insertBefore(emptyState, abstainContainer || null);
            }
        }
    }

    if (position) {
        const ballotForm = document.getElementById('ballot-form');
        const maxAllowed = Number(position.max_selections || position.seats || 1);
        const candidateInputs = Array.from(ballotForm?.querySelectorAll('input[name="presidential_ballot"]') || [])
            .filter((input) => input.value !== 'abstain');
        const abstainInput = ballotForm?.querySelector('input[value="abstain"]');

        if (ballotForm) {
            ballotForm.querySelectorAll('.candidate-option').forEach((card) => {
                card.classList.add('candidate-card', 'relative', 'flex', 'cursor-pointer', 'select-none', 'items-center', 'justify-between', 'rounded-xl', 'border', 'border-slate-300/60', 'bg-white', 'p-4', 'shadow-sm', 'transition-all', 'duration-150');
            });
            const tracker = document.createElement('div');
            tracker.className = 'mb-3 flex items-center justify-between rounded-lg bg-[#e7eeff] px-4 py-2 text-xs font-semibold uppercase tracking-wider text-[#44474c]';
            tracker.innerHTML = `<span>Candidate roster</span><strong id="ballot-selection-counter">SELECTED: 0 / ${maxAllowed}</strong>`;
            ballotForm.parentElement.insertBefore(tracker, ballotForm);
            const updateCounter = () => {
                const selectedCount = candidateInputs.filter((input) => input.checked).length;
                const counter = document.getElementById('ballot-selection-counter');
                if (counter) counter.textContent = abstainInput?.checked ? `ABSTAINED (0 / ${maxAllowed})` : `SELECTED: ${selectedCount} / ${maxAllowed}`;
            };
            const updateCandidateVisuals = () => {
                candidateInputs.forEach((input) => {
                    const card = input.closest('.candidate-option');
                    const indicator = card?.querySelector('.radio-indicator');
                    const dot = card?.querySelector('.dot');
                    indicator?.classList.toggle('border-[#0b192c]', input.checked);
                    indicator?.classList.toggle('bg-[#0b192c]', input.checked);
                    dot?.classList.toggle('scale-100', input.checked);
                    dot?.classList.toggle('scale-0', !input.checked);
                });
            };
            candidateInputs.forEach((input) => input.addEventListener('change', updateCounter));
            abstainInput?.addEventListener('change', updateCounter);
            updateCounter();
            updateCandidateVisuals();
            window.updateCandidateVisuals = updateCandidateVisuals;
        }

        if (maxAllowed >= 1) {
            candidateInputs.forEach((input) => {
                input.type = 'checkbox';
                input.addEventListener('change', () => {
                    if (input.checked && maxAllowed === 1) {
                        candidateInputs.forEach((candidate) => {
                            if (candidate !== input) candidate.checked = false;
                        });
                    } else if (candidateInputs.filter((candidate) => candidate.checked).length > maxAllowed) {
                        input.checked = false;
                    }
                    if (input.checked && abstainInput) abstainInput.checked = false;
                    submitBallotButton?.toggleAttribute('disabled', !candidateInputs.some((candidate) => candidate.checked) && !abstainInput?.checked);
                    const counter = document.getElementById('ballot-selection-counter');
                    if (counter) counter.textContent = abstainInput?.checked ? `ABSTAINED (0 / ${maxAllowed})` : `SELECTED: ${candidateInputs.filter((candidate) => candidate.checked).length} / ${maxAllowed}`;
                    window.updateCandidateVisuals?.();
                });
            });

            if (abstainInput) {
                abstainInput.type = 'checkbox';
                abstainInput.addEventListener('change', () => {
                    if (abstainInput.checked) candidateInputs.forEach((input) => { input.checked = false; });
                    submitBallotButton?.toggleAttribute('disabled', !abstainInput.checked && !candidateInputs.some((candidate) => candidate.checked));
                    window.updateCandidateVisuals?.();
                });
            }
        }
    }

    if (position) {
        const positionHeading = document.querySelector('main section h1, main section h2');
        const positionDescription = document.querySelector('main section p');
        const seatBadge = Array.from(document.querySelectorAll('main section span')).find((span) => span.textContent.includes('SEAT AVAILABLE'));
        if (positionHeading) positionHeading.textContent = position.name;
        if (seatBadge) seatBadge.textContent = `${position.seats} SEAT${position.seats === 1 ? '' : 'S'} AVAILABLE`;
        if (positionDescription && document.title.includes('Ballot Marking')) {
            positionDescription.textContent = `${position.rule === 'multi' ? 'Select up to' : 'Select'} ${position.max_selections} candidate${position.max_selections === 1 ? '' : 's'} · Secret & Certified Ballot`;
        }
    } else if (window.electionPosition === null && document.title.includes('Ballot Marking')) {
        const ballotForm = document.querySelector('#ballot-form, #board-ballot-form');
        const submitButton = document.querySelector('#submit-ballot-btn, #review-cta');
        const heading = document.querySelector('main section h2');
        if (heading) heading.textContent = 'No active position';
        ballotForm?.classList.add('hidden');
        submitButton?.classList.add('hidden');
    }

    if (window.electionDataUrl) {
        let lastUpdatedAt = null;
        let lastPositionSignature = null;
        let lastPositionStates = new Map();
        let lastBallotSubmissionSignature = null;
        const updateOverviewTimeline = (position, submissions = [], positions = []) => {
            const timeline = document.getElementById('election-timeline');
            if (!timeline) return;

            const noPositions = positions.length === 0 && !position;
            const allPositionsComplete = positions.length > 0 && positions.every((candidatePosition) => Boolean(candidatePosition.is_completed));
            let currentStep = 1;
            if (allPositionsComplete) currentStep = 5;
            else if (noPositions) currentStep = 1;
            else if (!position || position.is_completed || position.is_closed) currentStep = 5;
            else if (position.is_unlocked) currentStep = 4;
            else if (submissions.some((submission) => submission.status === 'Pending')) currentStep = 2;
            else if (submissions.length > 0) currentStep = 3;

            timeline.dataset.currentStep = String(currentStep);
            const stepLabel = document.getElementById('timeline-step-label');
            if (stepLabel) stepLabel.textContent = `Step ${currentStep} of 5`;
            document.getElementById('overview-active-actions')?.classList.toggle('hidden', allPositionsComplete);
            document.getElementById('overview-voter-access')?.classList.toggle('hidden', allPositionsComplete);
            document.getElementById('overview-complete-actions')?.classList.toggle('hidden', !allPositionsComplete);
            document.querySelectorAll('#overview-active-actions button[data-modal]').forEach((button) => {
                const isCandidacy = button.dataset.modal === 'modal-candidacy';
                const locked = noPositions || (isCandidacy ? !position?.candidacy_open : !position?.nomination_open);
                button.disabled = locked;
                const label = button.querySelector('[data-action-label]');
                const icon = button.querySelector('[data-action-icon]');
                if (label) label.textContent = locked ? (isCandidacy ? 'Candidacy locked' : 'Nomination locked') : (isCandidacy ? 'File candidacy' : 'Submit nomination');
                if (icon) icon.textContent = locked ? 'lock' : 'arrow_forward';
            });
            const voterAccessLink = document.querySelector('[data-voter-access-link]');
            if (voterAccessLink) {
                const locked = noPositions;
                if (!voterAccessLink.dataset.originalHref) voterAccessLink.dataset.originalHref = voterAccessLink.getAttribute('data-original-href') || voterAccessLink.getAttribute('href') || '#';
                voterAccessLink.classList.toggle('pointer-events-none', locked);
                voterAccessLink.classList.toggle('cursor-not-allowed', locked);
                voterAccessLink.classList.toggle('opacity-60', locked);
                voterAccessLink.setAttribute('aria-disabled', locked ? 'true' : 'false');
                voterAccessLink.setAttribute('href', locked ? '#' : (voterAccessLink.dataset.originalHref || '#'));
                const voterTitle = voterAccessLink.querySelector('[data-voter-access-title]');
                const voterDescription = voterAccessLink.querySelector('[data-voter-access-description]');
                if (voterTitle) voterTitle.textContent = locked ? 'Voting locked' : 'Ready to vote?';
                if (voterDescription) voterDescription.textContent = locked ? 'Create an election position before voting.' : `Continue directly to the ${position?.name || 'current'} ballot.`;
            }
            const overviewName = document.getElementById('current-position-name');
            const overviewStatus = document.getElementById('current-position-status');
            if (noPositions) {
                window.electionPosition = null;
                if (overviewName) overviewName.textContent = 'Election position';
                if (overviewStatus) overviewStatus.innerHTML = '<span class="material-symbols-outlined text-lg">lock_open</span>Election controls locked';
            } else if (allPositionsComplete) {
                if (overviewName) overviewName.textContent = 'Election complete';
                if (overviewStatus) overviewStatus.innerHTML = '<span class="material-symbols-outlined text-lg">verified</span>All ballot positions completed';
            } else if (position) {
                window.electionPosition = position;
                if (overviewName) overviewName.textContent = position.name;
                if (overviewStatus) overviewStatus.innerHTML = `<span class="material-symbols-outlined text-lg">${position.is_closed ? 'event_busy' : (position.is_unlocked ? 'how_to_vote' : 'lock_open')}</span>${position.is_closed ? 'Voting closed' : (position.is_unlocked ? 'Voting open' : 'Waiting for administrator to start')}`;
            }

            timeline.querySelectorAll('.timeline-step').forEach((step) => {
                const stepNumber = Number(step.dataset.step);
                const done = stepNumber < currentStep;
                const active = stepNumber === currentStep;
                const circle = step.querySelector('.timeline-circle');
                const name = step.querySelector('.timeline-name');
                const activeLabel = step.querySelector('.timeline-active');

                circle?.classList.toggle('bg-secondary', done || active);
                circle?.classList.toggle('text-white', done || active);
                circle?.classList.toggle('shadow-md', done || active);
                circle?.classList.toggle('bg-surface-container-highest', !done && !active);
                circle?.classList.toggle('text-on-surface-variant', !done && !active);
                if (circle) circle.innerHTML = `<span class="material-symbols-outlined text-base">${step.dataset.icon || 'radio_button_checked'}</span>`;
                name?.classList.toggle('font-bold', active);
                name?.classList.toggle('text-secondary', active);
                name?.classList.toggle('text-on-surface-variant', !active);
                if (active && !activeLabel) step.insertAdjacentHTML('beforeend', '<span class="timeline-active rounded bg-tertiary-fixed px-1 text-[9px] font-bold uppercase text-[#5a4300]">Active</span>');
                if (!active) step.querySelector('.timeline-active')?.remove();
            });
        };

        const canRefresh = () => {
            const active = document.activeElement;
            const editing = active && ['INPUT', 'TEXTAREA', 'SELECT'].includes(active.tagName);
            const selected = document.querySelector('input:checked');
            const modalOpen = document.querySelector('.modal.flex, dialog[open]');

            return !document.hidden && !editing && !selected && !modalOpen;
        };

        const pollElectionData = async () => {
            try {
                const response = await fetch(window.electionDataUrl, { headers: { Accept: 'application/json' }, cache: 'no-store' });
                if (!response.ok) return;
                const data = await response.json();
                const positionSignature = JSON.stringify((data.positions || []).map((candidatePosition) => [candidatePosition.id, candidatePosition.updated_at, candidatePosition.unlocked_at, candidatePosition.is_unlocked, candidatePosition.is_completed, candidatePosition.is_closed, candidatePosition.candidacy_open, candidatePosition.nomination_open]));
                const currentPositionStates = new Map((data.positions || []).map((candidatePosition) => [Number(candidatePosition.id), candidatePosition]));
                const openModal = document.querySelector('.modal.flex');
                const modalPosition = data.active_position;
                const previousModalPosition = modalPosition ? lastPositionStates.get(Number(modalPosition.id)) : null;
                if (openModal && previousModalPosition && modalPosition && Number(modalPosition.id) === Number(previousModalPosition.id)) {
                    if (openModal.id === 'modal-candidacy' && previousModalPosition.candidacy_open && !modalPosition.candidacy_open) {
                        showStageClosedError(openModal, 'Candidacy was closed by the administrator. Your submission was not accepted.');
                    }
                    if (openModal.id === 'modal-nomination' && previousModalPosition.nomination_open && !modalPosition.nomination_open) {
                        showStageClosedError(openModal, 'Nominations were closed by the administrator. Your submission was not accepted.');
                    }
                }
                lastPositionStates = currentPositionStates;
                const ballotSubmissionSignature = JSON.stringify((data.submissions || []).map((submission) => [submission.id, submission.position_id, submission.candidate_name, submission.status, submission.updated_at]));
                const isOverview = document.body.classList.contains('home-page');
                if (isOverview && lastPositionSignature !== null && positionSignature !== lastPositionSignature && canRefresh()) {
                    window.location.reload();
                    return;
                }
                if (lastPositionSignature === null || canRefresh()) lastPositionSignature = positionSignature;
                updateOverviewTimeline(data.active_position, data.submissions || [], data.positions || []);
                const revision = `${data.latest_updated_at || ''}|${data.latest_submission_updated_at || ''}`;
                if (lastUpdatedAt === null) {
                    lastUpdatedAt = revision;
                    lastBallotSubmissionSignature = ballotSubmissionSignature;
                    return;
                }
                const currentPositionId = Number(window.electionPosition?.id || 0);
                const activePosition = data.active_position;
                const isBallotMarking = document.title.includes('Ballot Marking');
                const isReviewVote = document.title.includes('Review Your Vote');
                const ballotSubmissionsChanged = lastBallotSubmissionSignature !== null
                    && ballotSubmissionSignature !== lastBallotSubmissionSignature;
                const ballotRefreshBlocked = isBallotMarking && ballotSubmissionsChanged && !canRefresh();
                if (isBallotMarking
                    && ballotSubmissionsChanged
                    && activePosition
                    && Number(activePosition.id) === currentPositionId
                    && canRefresh()) {
                    // Candidate approvals and nominations are returned by the same
                    // live endpoint, but the ballot roster was server-rendered only.
                    // Reloading here gives the open ballot tab the same live behavior
                    // as the overview and results screens without discarding a choice.
                    window.location.reload();
                    return;
                }
                if (activePosition && Number(activePosition.id) !== currentPositionId && !document.title.includes('Ballot Marking')) {
                    window.electionPosition = activePosition;
                    const positionName = document.getElementById('current-position-name');
                    const positionStatus = document.getElementById('current-position-status');
                    if (positionName) positionName.textContent = activePosition.name;
                    if (positionStatus) positionStatus.innerHTML = `<span class="material-symbols-outlined text-lg">${activePosition.is_closed ? 'event_busy' : 'lock_open'}</span>${activePosition.is_closed ? 'Voting closed' : 'Candidacy & nomination open'}`;
                }
                const livePosition = Array.isArray(data.positions)
                    ? data.positions.find((candidatePosition) => Number(candidatePosition.id) === currentPositionId)
                    : null;
                const lockStateChanged = livePosition
                    && (Boolean(livePosition.is_unlocked) !== Boolean(window.electionPosition?.is_unlocked)
                        || Boolean(livePosition.is_closed) !== Boolean(window.electionPosition?.is_closed));
                // Lock state is authoritative and must be applied even when the
                // timestamp revision is unchanged within the same second.
                if (lockStateChanged) {
                    window.electionPosition = { ...window.electionPosition, ...livePosition };
                    const positionStatus = document.getElementById('current-position-status');
                    if (positionStatus) positionStatus.innerHTML = `<span class="material-symbols-outlined text-lg">${livePosition.is_closed ? 'event_busy' : 'lock_open'}</span>${livePosition.is_closed ? 'Voting closed' : 'Candidacy & nomination open'}`;
                    if (livePosition.is_unlocked && !livePosition.is_closed) {
                        document.getElementById('ballot-lock-overlay')?.remove();
                        document.querySelectorAll('#ballot-form input').forEach((element) => { element.disabled = false; });
                        submitBallotButton?.toggleAttribute('disabled', !document.querySelector('#ballot-form input:checked'));
                        startBallotCountdown(livePosition.remaining_seconds || 60);
                    } else {
                        const existingOverlay = document.getElementById('ballot-lock-overlay');
                        if (!existingOverlay && (isBallotMarking || isReviewVote)) {
                            const message = livePosition.is_closed ? 'Voting is closed. Your ballot cannot be submitted.' : 'The administrator locked voting before your ballot was submitted.';
                            document.body.insertAdjacentHTML('beforeend', `<div class="fixed inset-x-0 bottom-0 top-20 z-30 flex items-center justify-center bg-slate-900/45 px-5 backdrop-blur-sm" id="ballot-lock-overlay"><div class="w-full max-w-md rounded-2xl bg-white p-8 text-center shadow-2xl"><span class="material-symbols-outlined text-5xl text-[#115cb9]">${livePosition.is_closed ? 'event_busy' : 'lock'}</span><h2 class="mt-4 font-display text-2xl font-bold">Voting unavailable</h2><p class="mt-2 text-sm leading-6 text-slate-600">${message}</p><div class="mt-6 grid gap-3 sm:grid-cols-2"><a href="${window.homeUrl || '/home'}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-4 py-3 text-sm font-bold text-[#0b192c]">Back to overview</a><a href="${window.resultsUrl || '/results'}" class="inline-flex items-center justify-center rounded-xl bg-[#115cb9] px-4 py-3 text-sm font-bold text-white">Show results</a></div></div></div>`);
                        }
                        document.querySelectorAll('#ballot-form input, #submit-ballot-btn').forEach((element) => { element.disabled = true; });
                        document.querySelectorAll('#review-vote-form input, #review-vote-form button, #review-vote-submit-form input, #submit-final-vote').forEach((element) => { element.disabled = true; });
                    }
                } else if (revision !== lastUpdatedAt && canRefresh()) {
                    const activePosition = data.active_position;
                    if (activePosition && window.electionPosition && Number(activePosition.id) === Number(window.electionPosition.id)) {
                        window.electionPosition = { ...window.electionPosition, ...activePosition };
                    }
                }
                lastUpdatedAt = revision;
                // Keep the previous signature while a voter has a selection or is
                // editing the ballot, so the next safe poll still applies the update.
                if (!ballotRefreshBlocked) lastBallotSubmissionSignature = ballotSubmissionSignature;
            } catch {
                // A temporary connection failure should not interrupt the voter screen.
            }
        };

        pollElectionData();
        window.setInterval(pollElectionData, 1000);
    }
});
