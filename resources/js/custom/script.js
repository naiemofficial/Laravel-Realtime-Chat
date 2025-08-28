function scrollMsg(el){
    const scrollBox = el.closest('div');
    if (scrollBox) {
        scrollBox.scrollTo({
            top: scrollBox.scrollHeight,
            behavior: 'smooth'
        });
    }
}

function revealAndScroll(n, index, el, delay) {
    setTimeout(() => {
        const component = Alpine.$data(el);
        component.show = true;

        Alpine.nextTick(() => {
            scrollMsg(el);
        });

        if(index === n-1){
            scrollMsg(el);
        }
    }, delay);
}
window.revealAndScroll = revealAndScroll;




function callDuration(call) {
    const timestamps = {
        from: (['accepted', 'ended'].includes(call.status) && call.accepted_at) ? call.accepted_at : '',
        to: call.ended_at ?? (call.last_ping ?? '')
    };

    if (!timestamps.from || !timestamps.to) return '';

    const from = new Date(timestamps.from);
    const to = new Date(timestamps.to);

    let seconds = Math.abs(Math.floor((to - from) / 1000));

    if (seconds < 60) {
        return `${seconds}s`;
    } else if (seconds < 3600) {
        const m = Math.floor(seconds / 60);
        const s = seconds % 60;
        return `${m}m${s > 0 ? ' ' + s + 's' : ''}`;
    } else {
        const h = Math.floor(seconds / 3600);
        const m = Math.floor((seconds % 3600) / 60);
        return `${h}hr${m > 0 ? ' ' + m + 'm' : ''}`;
    }
}

export function executeDropMessage(from, data) {
    return new Promise((resolve) => {
        const message = data.message ?? data;
        const call = message?.call ?? null;

        const ul = document.querySelector('#chat-box[role=list]');
        if (!ul) return;

        const last_sender_element   = ul.querySelector('li:last-child');
        const last_sender   = last_sender_element?.getAttribute('data-sender');
        const auth_user_id          = Number(auth.user.id);
        const message_user_id       = Number(message.user_id);


        const li = document.createElement('li');
        li.setAttribute('wire:key', message.id);
        li.setAttribute('x-data', '{ show: false }');
        li.setAttribute('x-init', 'revealAndScroll(1, 0, $el, 100)');
        li.setAttribute('x-show', 'show');
        li.setAttribute('x-transition.duration.300ms', '');
        li.setAttribute('data-type', message.type);
        li.className = `px-8 pt-3 transition-[0.3s] bg-white rounded-sm border-none`;
        li.setAttribute('data-participant', ((message_user_id === auth_user_id) ? 'self' : 'recipient'));
        li.style.transition = '0.3s';

        // ----------------------------------------------------------
        if (message.type === "regular") {
            // regular text message
            li.innerHTML = `
                <div class='block'>
                    <div class='inline-block bg-blue-500 text-white rounded-lg px-4 py-2 max-w-xs'>
                        <p class='text-sm leading-snug'>${message.text}</p>
                    </div>
                </div>
            `;
        }
        else if (message.type === "call" && call) {
            const voice_icon    = (message.user_id === auth?.user?.id) ? "fa-solid fa-phone-arrow-up-right" : "fa-solid fa-phone-arrow-down-left";
            const video_con     = (message.user_id === auth?.user?.id) ? "fa-solid fa-video-arrow-up-right" : "fa-solid fa-video-arrow-down-left";
            const icon          = (call.type === "voice") ? voice_icon : video_con;
            const icon_color    = (call.status === "cancelled") ? "text-red-500" : "";
            const pre_ext       = (call.status === "cancelled") ? "Missed" : "";
            const text          = `${pre_ext} ${call.type} ${message.type}`;
            const status        = ["busy", "declined", "accepted", "ended"].includes(call?.status) ? call.status : "";
            const duration      = callDuration(call);


            li.innerHTML = `
                <div class='block'>
                    <div class='inline-block bg-gray-100 border border-gray-200 text-gray-700 rounded-lg px-4 py-3 max-w-xs'>
                        <div class='inline-flex flex-row items-center text-xs leading-snug capitalize gap-2'>
                            <i class='${icon} ${icon_color}'></i>
                            <div class='inline-flex flex-col gap-0 leading-snug text-left'>
                                <span class='font-medium'>${text}</span>
                                ${
                                    (['accepted', 'ended'].includes(call.status) && duration)
                                    ? `<span style="zoom: 0.9">${duration}</span>`
                                    : (status ? `<span style="zoom: 0.9">${status}</span>` : '')
                                }
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        else {
            // fallback bubble
            li.innerHTML = `
                <div class='block'>
                    <div class='inline-block border border-gray-100 rounded-full'>
                        <p class='text-sm leading-snug'>${message.text}</p>
                    </div>
                </div>
            `;
        }

        // tooltip (timestamp)
        const tooltip = `
            <div class='msg-tooltip absolute bottom-full left-1/2 mb-2 hidden group-hover:block bg-gray-800 text-white text-xs rounded px-2 py-1 z-10 whitespace-nowrap'
                 style='transform: translateX(-50%)'>
                ${new Date(message.created_at).toLocaleString("en-US", {
                    month: "short", day: "numeric", year: "numeric",
                    hour: "numeric", minute: "2-digit", hour12: true
                })}
            </div>
        `;
        // ----------------------------------------------------------

        const selectedConversationElement = document.querySelector('#conversations[role="list"] li[aria-selected="true"]');
        const selectedConversation =  Number(selectedConversationElement?.getAttribute('wire:key'));
        const openedConversationElement = document.querySelector('#chat-box[role="list"]');
        const openedConversation = Number(openedConversationElement?.dataset?.conversation);
        const targetConversationId = Number(message.conversation_id);

        if(targetConversationId === selectedConversation && selectedConversation === openedConversation){
            const existing_li = ul?.querySelector(`li[wire\\:key="${message.id}"]`);
            if(existing_li){
                existing_li.replaceWith(li);
            } else {
                ul.appendChild(li);
            }
            resolve({
                status: true,
                data: {
                    openedConversation
                }
            });
        }
        resolve({
            status: false
        })
    });
}














function formatCallTime(elapsed) {
    const hours = Math.floor(elapsed / 3600);
    const minutes = Math.floor((elapsed % 3600) / 60);
    const seconds = elapsed % 60;

    if (hours > 0) {
        return (
            String(hours).padStart(2, '0') + ':' +
            String(minutes).padStart(2, '0') + ':' +
            String(seconds).padStart(2, '0')
        );
    } else {
        return (
            String(minutes).padStart(2, '0') + ':' +
            String(seconds).padStart(2, '0')
        );
    }
}

async function init_Call(wire, sendingCall, Call, settings, peerSettings) {
    const callDiv = document.querySelector('#call');
    if (!callDiv) return;

    if (callDiv.callInterval) {
        clearInterval(callDiv.callInterval);
    }







    const video_feed = document.querySelector('#call #video-feed');
    if(Call?.type === 'video' && video_feed){
        startVideoStream();
        const video_local_div = video_feed?.querySelector('video[x-ref="localVideo"]')?.closest('div');
        if(video_local_div && ['pending', 'accepted'].includes(Call?.status)){
            if(Call.status === 'pending'){
                Object.assign(video_local_div.style, { top:'0', right:'0', width:'100%', height:'100%' });
            } else if(Call.status === 'accepted'){
                video_local_div.removeAttribute('style');
            }
        }
    } else {
        video_feed?.remove();
    }
    // await getLocalMedia(Call?.type);







    const startTime = new Date();

    callDiv.callInterval = setInterval(async () => {
        const span = callDiv.querySelector('#call-text');
        const callExist = document.querySelector('#call');
        if(!span || !callExist){
            clearInterval(callDiv.callInterval);
            delete callDiv.callInterval;
            return;
        }

        const elapsed = Math.floor((new Date() - startTime) / 1000);

        if(elapsed % 3 === 0){
            if (Call?.type === 'video') {
                const video_feed = callExist?.querySelector('#video-feed');
                if (video_feed && wire?.updateTemp) {
                    // wire.updateTemp('video-feed-style', {
                    //     left: video_feed.style.left || null,
                    //     top: video_feed.style.top || null,
                    //     position: video_feed.style.position || null
                    // });
                }
            }
        }

        if(Call?.status === 'pending'){
            if (elapsed >= settings.ringTime) {
                if(sendingCall){
                    clearInterval(callDiv.callInterval);
                    delete callDiv.callInterval;
                    wire.cancelDeclineEndCall();
                } else {
                    // Disable action buttons
                }
            } else if(peerSettings?.ringing === false){
                // Re-try call if recipient not connected
                if(elapsed % 3 === 0){
                    wire?.broadcastCall(true, {skipBusy: true});
                }
            }
        } else if (Call?.status === 'accepted') {
            const time = callDiv.querySelector('#call-text > time');
            time?.setAttribute('data-text', formatCallTime(elapsed));

            if(callDiv.pingInterval){
                clearInterval(callDiv.pingInterval);
                delete callDiv.pingInterval;
            }
            if(elapsed % 3 === 0){
                // wire?.pingCall();
            }
        }
    }, 1000);



    let p = 1;
    callDiv.pingInterval = setInterval(() => {
        const ping = callDiv.querySelector('#call-text .ping');
        if (ping) ping.innerText = ['', '.', '..', '...'][p % 4];
        p++;
    }, 400)
}









































// --------------- START - Video Call Drag
function setVideoFeedPosition(el){
    const rect = el.getBoundingClientRect();
    const left = (rect.width/2) + 'px';
    el.style.left = `calc(50% - ${left})`;
    el.style.top = '5px';
    el.style.position = 'fixed';
    el.style.transform = '';
}


document.addEventListener('mousedown', (event) => {
    const target = event?.target;
    const videoFeed = target?.id === 'video-feed' ? target : target?.closest('#video-feed');
    if (!videoFeed) return;

    event.preventDefault();
    videoFeed.classList.add('dragging');

    // Calculate offset of mouse inside element
    const rect = videoFeed.getBoundingClientRect();
    const offsetX = event.clientX - rect.left;
    const offsetY = event.clientY - rect.top;

    videoFeed.style.position = 'fixed';

    function onMouseMove(e) {
        videoFeed.style.left = e.clientX - offsetX + 'px';
        videoFeed.style.top = e.clientY - offsetY + 'px';
    }

    function onMouseUp() {
        videoFeed.classList.remove('dragging');
        document.removeEventListener('mousemove', onMouseMove);
        document.removeEventListener('mouseup', onMouseUp);
    }

    document.addEventListener('mousemove', onMouseMove);
    document.addEventListener('mouseup', onMouseUp);
});
// --------------- END - Video Call Drag











export async function checkLocalMediaPermissions(callType) {
    callType = Array.isArray(callType) ? callType[0] : (typeof callType === 'string' ? callType : null);
    if (!['voice', 'video'].includes(callType)) return callType;

    const notify = msg => {
        if (Livewire) {
            Livewire.dispatch('refresh-message-alert', { response: { error: msg }, end_preference: { html: true } });
        } else {
            alert(msg);
        }
    };

    if (callType === 'voice') {
        try {
            const mic = await navigator.mediaDevices.getUserMedia({ audio: true });
            return { mic, camera: null };
        } catch {
            notify('<i class="fas fa-microphone"></i> Microphone access denied or unavailable.');
            return { mic: null, camera: null };
        }
    }

    if (callType === 'video') {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ audio: true, video: true });
            return { mic: stream, camera: stream };
        } catch {
            notify('<i class="fas fa-video"></i> Camera or microphone access denied or unavailable.');
            return { mic: null, camera: null };
        }
    }
}


export async function startVoiceStream() {

}
export async function startVideoStream() {
    let localStream = await navigator.mediaDevices.getUserMedia({ audio: true, video: true });
    const localVideo = document.querySelector('#call #video-feed [x-ref="localVideo"]');
    localVideo.srcObject = localStream;
}

export async function stopVoiceStream() {
    return await navigator.mediaDevices.getUserMedia({ audio: false });
}
export async function stopVideoStream() {
    return await navigator.mediaDevices.getUserMedia({ audio: false, video: false });
}


























// Bind with window -----------------------------------------
Object.assign(window, {
    revealAndScroll,
    init_Call,
    setVideoFeedPosition,
    checkLocalMediaPermissions,
    startVoiceStream,
    startVideoStream,
    stopVoiceStream,
    stopVideoStream
});
