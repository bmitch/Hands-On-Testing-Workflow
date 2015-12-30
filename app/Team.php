<?php

namespace app;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $fillable = ['name', 'size'];

    public function add($user)
    {
        $this->guardAgainstTooManyMembers();

        $method = $user instanceof User ? 'save' : 'saveMany';

        $this->members()->$method($user);
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

    protected function guardAgainstTooManyMembers()
    {
        if ($this->count() >= $this->size) {
            throw new \Exception();
        }
    }
}
