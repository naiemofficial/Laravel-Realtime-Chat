import MessageSent from "./app/Events.js";
if(auth?.user?.id){
    Echo.private(`conversation-connection.${auth.user.id}`).listen('ConversationConnection', async (response) => {
        MessageSent(response);
    });
}
