import {
    executeDropMessage,
    manageRTC,
} from "../custom/script.js";

export default async function Conversation(response){
    const data = response.data;
    if(data?.to === 'CALL'){
        if(data?.rtc === true){
            manageRTC(data);
        } else {
            Livewire.dispatch('WS_Receive', {response: data});
        }
    } else if(response?.Conversation && response?.Sender && response?.Message){
        Livewire.dispatch('refresh-conversations');

        const message = response.Message;

        if(message.type === 'starter'){

        } else if(message.type === 'regular'){
            if (typeof executeDropMessage === 'function') {
                const response = await executeDropMessage('websocket', message);
                if(response.status){
                    const data = response.data;
                    Livewire.dispatch('refresh-conversations');
                    Livewire.dispatch('seen-conversation-incoming-message', { openedConversation: data.openedConversation });
                }
            }
        }

        else if(message.type === 'call'){
            Livewire.dispatch('incoming-call', {message_id: message?.id, data: data});
        }
    }
}
