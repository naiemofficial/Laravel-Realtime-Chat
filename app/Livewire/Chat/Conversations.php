<?php

namespace App\Livewire\Chat;

use App\Helpers\Filter;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Conversations extends Component
{
    use WithPagination;
    public array $filter = [];
    public array $search = [];
    public $openedConversation = -1;
    public $animation = true;


    #[On('open-Conversation')]
    public function openConversation(int $id): void {
        $this->openedConversation = $id;
        $this->dispatch('view-conversation', id: $id);
    }


    #[On('refresh-conversations')]
    public function render($data = [])
    {
        $this->animation = isset($data['animation']) ? $data['animation'] : $this->animation;

        // Conversations
        $conversations = Auth::user()?->conversations() ?? new Collection();

        $filter_data = [
            'order' => [
                'column' => 'updated_at',
            ],
            'pagination' => [
                'limit' => 5,
            ]
        ];
        extract(empty($this->filter) ? Filter::prepare($filter_data) : $this->filter);

        // Search Term
        if(!empty($this->search)){
            $conversations = Filter::search($todos, $this->search);
        }

        $conversations = ($conversations->count() > 0) ? $conversations->orderBy($order_column, $order_direction)->paginate($per_page) : $conversations;

        return view('livewire.chat.conversations', [
            'conversations' => $conversations,
            'className'     => $this::class,
        ]);
    }
}
