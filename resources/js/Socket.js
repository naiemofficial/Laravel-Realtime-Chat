import Conversation from "./app/Events.js";
if(typeof auth !== 'undefined' && auth?.user?.id){
    Echo.private(`conversation-connection.${auth.user.id}`).listen('ConversationConnection', async (response) => {
        Conversation(response);
    });



}
