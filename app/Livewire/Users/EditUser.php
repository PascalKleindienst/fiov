<?php

declare(strict_types=1);

namespace App\Livewire\Users;

use App\Attributes\TranslatedFormFields;
use App\Concerns\WithTranslatedFields;
use App\Enums\UserLevel;
use App\Models\User;
use Flux\Flux;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[TranslatedFormFields('users.fields.')]
final class EditUser extends Component
{
    use WithTranslatedFields;

    public User $user;

    #[Validate]
    public string $email;

    #[Validate(['required', 'string', 'max:255'])]
    public string $name;

    #[Validate(['required', new Enum(UserLevel::class)])]
    public string $level = UserLevel::User->value;

    /**
     * @return array<string, array<int, \Illuminate\Validation\Rules\Unique|string>>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->user->id)],
        ];
    }

    public function mount(): void
    {
        $this->fill([
            'email' => $this->user->email,
            'name' => $this->user->name,
            'level' => $this->user->level->value,
        ]);
    }

    public function save(): void
    {
        $this->authorize('update', $this->user);
        $this->validate();

        $this->user->update($this->only(['email', 'name', 'level']));

        Flux::toast(__('general.changes_have_been_saved'), __('general.status.success'), variant: 'success');
        Flux::modal('edit-user-'.$this->user->id)->close();
        $this->redirect(route('admin.users'), navigate: true);
    }
}
