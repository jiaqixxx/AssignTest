<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\AddressBook
 *
 * @property int $id
 * @property string|null $company
 * @property string|null $suburb
 * @property string|null $street
 * @property string|null $postcode
 * @property string|null $city
 * @property string|null $state
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AddressBook whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AddressBook whereCompany($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AddressBook whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AddressBook whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AddressBook wherePostcode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AddressBook whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AddressBook whereStreet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AddressBook whereSuburb($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AddressBook whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AddressBook extends Model
{
    //
}
