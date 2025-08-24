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
            const status    = ["busy", "declined", "accepted", "ended"].includes(call?.status) ? call.status : "";

            li.innerHTML = `
                <div class='block'>
                    <div class='inline-block bg-gray-100 border border-gray-200 text-gray-700 rounded-lg px-4 py-3 max-w-xs'>
                        <div class='inline-flex flex-row items-center text-xs leading-snug capitalize gap-2'>
                            <i class='${icon} ${icon_color}'></i>
                            <div class='inline-flex flex-col gap-0 leading-snug text-left'>
                                <span class='font-medium'>${text}</span>
                                ${status.length > 0 ? `<span style='zoom:0.9'>${status}</span>` : ""}
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
            ul.appendChild(li);
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

function init_Call(wire, Call, settings) {
    const callDiv = document.querySelector('#call');
    if (!callDiv) return;

    if (callDiv?.callInterval) {
        // clearInterval(callDiv.callInterval);
    } else {
        const startTime = new Date();

        callDiv.callInterval = setInterval(() => {
            const time = callDiv.querySelector('#call-text > time');
            if (!time) {
                clearInterval(callDiv.callInterval);
                delete callDiv.callInterval;
                return;
            }

            const elapsed = Math.floor((new Date() - startTime) / 1000);

            if (Call?.status === 'accepted') {
                const callTime = formatCallTime(elapsed);
                time.setAttribute('data-text', callTime);

                if(elapsed % 3 === 0) {
                    wire?.pingCall();
                }
            } else if (elapsed >= settings.ringTime) {
                clearInterval(callDiv.callInterval);
                delete callDiv.callInterval;
                wire?.cancelDeclineEndCall();
            }
        }, 1000);


        let p = 1;
        callDiv.pingInterval = setInterval(() => {
            const ping = callDiv.querySelector('#call-text .ping');
            if(ping){
                if (ping) ping.innerText = ['.', '..', '...'][p % 3]
            }
            p++;
        }, 300)
    }
}








// Bind with window -----------------------------------------
Object.assign(window, {
    revealAndScroll, init_Call
});
