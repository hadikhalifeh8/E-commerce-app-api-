<?php

namespace App\Http\Controllers\Api\pages;

use App\Http\Controllers\Controller;
use App\Models\address;
use App\Models\User;
use Illuminate\Http\Request;

class AddressController extends Controller
{


    public function addaddress($usersid,Request $request)
    {
        $users = User::find($usersid);

        if(!($users))
        {
         return response()->json([
                                 'status' => 'failure',
                                 'data' => 'Not found this userid ' . $usersid ,
                                ]);
        }

        if(($users))
          {
            
             $addAddress = new address();

             $addAddress->user_id = $usersid;
             $addAddress->address_name = $request->address_name;
             $addAddress->city = $request->city;
             $addAddress->street = $request->street;
             $addAddress->adress_latitude = $request->adress_latitude;
             $addAddress->adress_longitude = $request->adress_longitude;

             $addAddress->save();

             return response()->json([
                                  'status' => 'success',
                                  'data' => $addAddress,
                                  ]);

            }else{
                return response()->json([
                    'status' => 'failure',
                    'data' => 'Errrrrrror Not Saved YET',
                 ]);
            }
    }



    public function editaddress($usersid, Request $request)
    {

        $users = User::find($usersid);
        if(!($users))
        {
         return response()->json([
                                 'status' => 'failure',
                                 'data' => 'Not found this userid ' . $usersid ,
                                ]);
        }


        // $address = address::where('user_id',$usersid);
        $address = address::findOrFail($request->id);
        
        if(($address))
          {
            $address->address_name = $request->address_name;
             $address->city = $request->city;
             $address->street = $request->street;
             $address->adress_latitude = $request->adress_latitude;
             $address->adress_longitude = $request->adress_longitude;

             $address->save();

             return response()->json([
                                  'status' => 'success',
                                  'data' => $address,
                                  ]);

            }else{
                return response()->json([
                    'status' => 'failure',
                    'data' => 'Errrrrrror Not Saved YET',
                 ]);
            }
    }


    public function viewaddress($usersid)
    {
        $users = User::find($usersid);
        if(!($users))
        {
         return response()->json([
                                 'status' => 'failure',
                                 'data' => 'Not found this userid ' . $usersid ,
                                ]);
        }

        $address = address::where('user_id',$usersid)->with('user_rltn')->get();

        if($users) {
            return response()->json([
                'status' => 'success',
                'data' => $address ,
               ]);

        }else{
            return response()->json([
                                      'status' => 'failure',
                                     'data' => 'Not Address Data found this userid ' . $usersid ,
               ]);

        }
    }


    public function deleteaddress($addressid)
    {

        $address = address::where('id', $addressid)->delete();

        if($address)
        {
           
         return response()->json([
                               'status' => 'success',
                                'data' => $address,
                 ]);
        } else{
           
              return response()->json([
                                    'status' => 'failure',
                                     'data' => 'No data',
                      ]);
             }

    }



}
