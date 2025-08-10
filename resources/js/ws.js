if(typeof Livewire != "undefined"){
    Livewire.on('user.updated', (currentUser) => {
        let user = currentUser[0];

        console.log(user);
        if(user){
            console.log(`App.Models.User.${user.id}`);
            Echo
                .private(`App.Models.User.${user.id}`)
                .listen('MessageSent', (event) => {
                    console.log(event);
                });
        }
    });
} else {
    Echo
        .private(`App.Models.User.1`)
        .listen('MessageSent', (event) => {
            console.log(event);
        });
}


