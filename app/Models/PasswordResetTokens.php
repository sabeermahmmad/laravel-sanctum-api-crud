<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordResetTokens extends Model
{
    protected $table = 'password_reset_tokens';

    // Disable primary key auto-incrementing
    public $incrementing = false;

    // No primary key column (or use 'email' if that's your PK)
    protected $primaryKey = 'email';

    // Disable timestamps if your table doesn't have created_at/updated_at
    public $timestamps = false;

    // If your primary key is not an integer
    protected $keyType = 'string';

    // Allow mass assignment
    protected $fillable = ['email', 'token', 'created_at'];
}
