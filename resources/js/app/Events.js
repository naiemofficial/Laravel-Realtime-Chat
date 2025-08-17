import {executeDropMessage} from "../custom/functions.js";

export default async function Conversation(response){
    if(response?.Conversation && response?.Sender && response?.Message){
        Livewire.dispatch('refresh-conversations');

        const message = response.Message;
        if(message.type !== 'starter'){
            console.log(message);
            if(message.type === 'regular'){
                if (typeof executeDropMessage === 'function') {
                    const response = await executeDropMessage('websocket', message);
                    if(response.status){
                        const data = response.data;
                        Livewire.dispatch('refresh-conversations');
                        Livewire.dispatch('seen-conversation-incoming-message', { openedConversation: data.openedConversation });
                    }
                }
            } else if(message.type === 'call'){
                const data = {
                    'conversation_id': message.conversation_id
                };
                Livewire.dispatch('incoming-call', {data: data});
            } else if(message.type === 'RESPONSE'){
                console.log(`${message.type} ${message.text}`);
            }
        }
    }
}
