<?php

namespace App\Models;

use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use LogicException;

/**
 * Class User
 * @package App\Models
 *
 * Attributes
 * @property int $id
 * @property int|null $manager_id
 * @property string $name
 * @property string $email
 * @property string $plea
 *
 * Relations
 * @property User|null $manager
 * @property Collection|User[] $reports
 * @property Collection|User[] $dominion
 * @property Collection|User[] $chainOfCommand
 *
 * Inheritance
 * @mixin Eloquent
 */
class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * @return HasMany
     */
    public function reports(): HasMany
    {
        return $this->hasMany(__CLASS__, 'manager_id');
    }

    /**
     * @return BelongsTo
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(__CLASS__, 'manager_id');
    }

    /**
     * @param bool $flatten
     * @return void
     * @throws Exception
     */
    public function fire(bool $flatten = true): void
    {
        if ($this->manager_id === null) {
            throw new LogicException(sprintf(
                'Unable to fire %s, instead you are now fired',
                $this->name
            ));
        }
        if ($flatten) {
            $this->reports->each(function (User $child): void {
                 $child->manager_id = $this->manager_id;
                 $child->save();
            });
        }
        $this->delete();
        $message = sprintf('%s: %s', $this->name, $this->plea);
        echo $message . "\n";
    }

    /**
     * @return void
     * @throws Exception
     */
    public function fireTeam(): void
    {
        $this->fire(false);
        $this->dominion
            ->each(function (User $user): void {
                $user->fire(false);
            });
    }

    /**
     * @return Collection|User[]
     */
    public function getDominionAttribute(): Collection
    {
        $sql = <<<'SQL'
        WITH RECURSIVE reports AS (
            -- CTE_query_definition (non-recursive term)
            SELECT * FROM users WHERE id = ? -- starting user
          
            UNION ALL
            
            -- CTE_query definition (recursive term)
            SELECT r.* 
            FROM users as r
            INNER JOIN reports ON reports.id = r.manager_id
            WHERE r.deleted_at IS NULL
        ) SELECT * FROM reports WHERE reports.id != ?;
SQL;
        return self::hydrate(DB::connection()->select($sql, [$this->id, $this->id]));
    }

    /**
     * @return Collection|User[]
     */
    public function getChainOfCommandAttribute(): Collection
    {
        $sql = <<<'SQL'
        WITH RECURSIVE chain AS (
            -- CTE_query_definition (non-recursive term)
            SELECT * FROM users WHERE id = ? -- starting user
          
            UNION ALL
            
            -- CTE_query definition (recursive term)
            SELECT r.* 
            FROM users as r
            INNER JOIN chain ON chain.manager_id = r.id
            WHERE r.deleted_at IS NULL
        ) SELECT * FROM chain WHERE chain.id != ?;
SQL;
        return self::hydrate(DB::connection()->select($sql, [$this->id, $this->id]));
    }
}
