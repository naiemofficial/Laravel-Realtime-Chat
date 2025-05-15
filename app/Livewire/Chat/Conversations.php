<?php

namespace App\Livewire\Chat;

use App\Models\Conversation;
use Livewire\Component;
use App\Helpers\Filter;
use App\Models\Guest;
use Illuminate\Database\Eloquent\Collection;

class Conversations extends Component
{
    public array $filter = [];
    public array $search = [];


    public function openConversation(int $id){
        $this->dispatch('view-conversation', id: $id);
    }



    public function render()
    {
        // Todos
        $conversations = Guest::current()?->conversations() ?? new Collection();

        extract(empty($this->filter) ? Filter::prepare([]) : $this->filter);

        // Search Term
        if(!empty($this->search)){
            $conversations = Filter::search($todos, $this->search);
        }

        $conversations = ($conversations->count() > 0) ? $conversations->orderBy($order_column, $order_direction)->paginate($per_page) : $conversations;

        return view('livewire.chat.conversations', [
            'conversations' => $conversations,
            'className'     => $this::class,
            'currentGuest'  => Guest::current()
        ]);
    }
}
