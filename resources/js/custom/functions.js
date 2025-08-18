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

        li.innerHTML = `
            <div class='block'>
                <div class='inline-block bg-blue-500 text-white rounded-lg px-4 py-2 max-w-xs'>
                    <p class='text-sm leading-snug'>${message.text}</p>
                </div>
            </div>
        `;

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
















// Bind with window -----------------------------------------
Object.assign(window, {
    revealAndScroll
});
