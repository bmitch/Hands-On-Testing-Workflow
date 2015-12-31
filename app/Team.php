<?php

namespace app;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $fillable = ['name', 'size'];

    public function add($users)
    {
        $this->guardAgainstTooManyMembers($users);

        $method = $users instanceof User ? 'save' : 'saveMany';

        $this->members()->$method($users);
    }

    public function remove($user = null)
    {
        if ($user instanceof User) {
            return $user->update(['team_id' => null]);
        }

        foreach ($user as $singleUser) {
            $this->remove($singleUser);
        }
    }

    public function purge()
    {
        foreach ($this->members() as $user) {
            $this->remove($user);
        }
    }

    public function members()
    {
        return $this->hasMany(User::class);
    }

    public function count()
    {
        return $this->members()->count();
    }

    public function maximumSize()
    {
        return $this->size;
    }

    protected function guardAgainstTooManyMembers($users)
    {
        $newTeamCount = $this->count() + $this->extractNewUsersCount($users);

        if ($newTeamCount > $this->maximumSize()) {
            throw new \Exception();
        } 
    }

    protected function extractNewUsersCount($users)
    {
        return ($users instanceof User) ? 1 : count($users);
    }

}
