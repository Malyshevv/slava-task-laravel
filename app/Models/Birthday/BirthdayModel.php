<?php

namespace App\Models\Birthday;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BirthdayModel extends Model
{
    use HasFactory;

	protected $table = 'birthday';
	public $incrementing = true;
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'id',
		'external_id',
		'name',
		'date',
		'created_at',
		'updated_at',
	];
	protected $primaryKey = 'id';
}
