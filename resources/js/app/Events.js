import {executeDropMessage} from "../custom/functions.js";

export default async function Conversation(response){
    if(Object.keys(response?.data).length > 0){
        const _response = response?.data;
        if(_response?.to === 'CALL'){
            Livewire.dispatch('WS_Receive', {response: _response});
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
            Livewire.dispatch('incoming-call', {message_id: message?.id});
        }
    }
}
