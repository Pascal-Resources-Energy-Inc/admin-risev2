<?php

namespace App\Http\Controllers;
use App\Stove;
use App\User;
use App\TransactionDetail;
use Illuminate\Http\Request;
use App\Client;
use App\Center;
use RealRashid\SweetAlert\Facades\Alert;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Facades\Schema;

class CustomerController extends Controller
{
    //
    public function index(Request $request)
    {   
        $activeCustomers = Client::where('status', 'Active')->count();
        $inactiveCustomers = Client::where('status', 'Inactive')->count();

        $centers = Center::get();
        $stoves = Stove::where('client_id',null)->get();
        $customers = Client::with(['transactions', 'serial'])->get();
        return view('customers',
            array(
                'stoves' => $stoves,
                'customers' => $customers,
                'centers' => $centers,
                'activeCustomers' => $activeCustomers,
                'inactiveCustomers' => $inactiveCustomers
            )
        );
    }
    public function view(Request $request,$id)
    {
        $transactions = TransactionDetail::where('client_id',$id)->orderBy('id','desc')->get();
        $customer = Client::with(['user', 'serial'])->findOrfail($id);
        $centers = Center::get();
        $stoves = Stove::whereNull('client_id')
            ->orWhere('client_id', $customer->id)
            ->get();

        return view('customer',
            array(
                'customer' => $customer,
                'transactions' => $transactions,
                'centers' => $centers,
                'stoves' => $stoves,
                
            )
        );
    }
    public function show(Request $request)
    {
        return view('customer-dashboard');
    }
    public function newCustomer(Request $request)
    {
        $stoves = Stove::where('client_id',null)->get();
        return view('new-customer',
            array(
                'stoves' => $stoves
            )
        );
    }

    public function saveCustomer(Request $request)
    {
        $fullName = trim(collect([
            $request->first_name,
            $request->middle_name,
            $request->last_name,
        ])->filter()->implode(' '));

        $user = new User;
        // $user->name = $fullName;
        $user->first_name = $request->first_name;
        $user->middle_name = $request->middle_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email_address;
        $user->role = 'Client';
        $user->birthdate = $request->birthdate;
        $user->age = $request->age;
        $user->password = bcrypt('12345678');
        $user->save();

        // Generate Client Reference
        $latestClient = Client::orderBy('id', 'desc')->first();

        if ($latestClient && $latestClient->client_reference) {
            $number = intval(substr($latestClient->client_reference, 3)) + 1;
        } else {
            $number = 1;
        }

        $client_reference = 'PRC' . str_pad($number, 5, '0', STR_PAD_LEFT);

        $customer = new Client;
        $customer->client_reference = $client_reference;
        $customer->user_id = $user->id;
        $customer->name = $fullName;
        $customer->email_address = $request->email_address;
        $customer->number = $request->phone_number;
        $customer->facebook = $request->facebook;
        $customer->address = $request->address;
        $customer->serial_number = $request->serial_number;
        $customer->location_region = $request->location_region;
        $customer->location_province = $request->location_province;
        $customer->location_city = $request->location_city;
        $customer->location_barangay = $request->location_barangay;
        $customer->postal_code = $request->postal_code;
        $customer->street_address = $request->street_address;
        $customer->spo = $request->spo;
        $customer->center = $request->center;
        $customer->status = $request->status;
        if (Schema::hasColumn('clients', 'latitude')) {
            $customer->latitude = $request->latitude;
        }
        if (Schema::hasColumn('clients', 'longitude')) {
            $customer->longitude = $request->longitude;
        }
        $customer->save();

        $serial_number = Stove::findOrfail($request->serial_number);
        $serial_number->client_id = $customer->id;
        $serial_number->save();


        Alert::success('Successfully encoded')->persistent('Dismiss');
        return redirect('view-client/' . $customer->id);
    }

    public function update(Request $request, $id)
    {
        $customer = Client::findOrFail($id);

        $fullName = trim(collect([
            $request->first_name,
            $request->middle_name,
            $request->last_name,
        ])->filter()->implode(' '));

        if ($customer->user_id) {
            User::where('id', $customer->user_id)->update([
                'name' => $fullName ?: $customer->name,
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'last_name' => $request->last_name,
                'email' => $request->email_address,
                'birthdate' => $request->birthdate,
                'age' => $request->age,
            ]);
        }

        $newSerialId = $request->serial_number;
        if ($newSerialId && (int) $newSerialId !== (int) $customer->serial_number) {
            if ($customer->serial_number) {
                $oldSerial = Stove::find($customer->serial_number);
                if ($oldSerial && (int) $oldSerial->client_id === (int) $customer->id) {
                    $oldSerial->client_id = null;
                    $oldSerial->save();
                }
            }

            $newSerial = Stove::findOrFail($newSerialId);
            $newSerial->client_id = $customer->id;
            $newSerial->save();
            $customer->serial_number = $newSerialId;
        }

        $customer->name = $fullName ?: $customer->name;
        $customer->email_address = $request->email_address;
        $customer->number = $request->number;
        $customer->facebook = $request->facebook;
        $customer->address = $request->address;
        $customer->location_region = $request->location_region;
        $customer->location_province = $request->location_province;
        $customer->location_city = $request->location_city;
        $customer->location_barangay = $request->location_barangay;
        $customer->postal_code = $request->postal_code;
        $customer->street_address = $request->street_address;
        $customer->spo = $request->spo;
        $customer->center = $request->center;
        $customer->status = $request->status;
        if (Schema::hasColumn('clients', 'latitude')) {
            $customer->latitude = $request->latitude;
        }
        if (Schema::hasColumn('clients', 'longitude')) {
            $customer->longitude = $request->longitude;
        }
        $customer->save();

        Alert::success('Success', 'Customer updated successfully!')->persistent('Dismiss');
        return redirect()->back();
    }
    
    public function changeAvatar(Request $request, $id)
    {
        $customer = Client::findOrfail($id);
        
        $imageData = $request->image_data;
        
        if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $matches)) {
            $imageType = $matches[1];
            $imageData = substr($imageData, strpos($imageData, ',') + 1);
        } else {
            Alert::error('Invalid image format')->persistent('Dismiss');
            return back();
        }
        
        $imageData = base64_decode($imageData);
        
        if ($imageData === false) {
            Alert::error('Failed to decode image')->persistent('Dismiss');
            return back();
        }
        
        $directory = public_path('avatar-client');
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }
        
        $fileName = 'avatar_' . $customer->id . '_' . time() . '.png';
        $filePath = $directory . '/' . $fileName;
        
        if (file_put_contents($filePath, $imageData)) {
            if ($customer->avatar && 
                $customer->avatar !== url('design/assets/images/profile/user-1.png') && 
                file_exists(public_path(str_replace(url('/'), '', $customer->avatar)))) {
                unlink(public_path(str_replace(url('/'), '', $customer->avatar)));
            }
            
            $customer->avatar = 'avatar-client/' . $fileName;
            $customer->save();
            
            Alert::success('Successfully Uploaded')->persistent('Dismiss');
        } else {
            Alert::error('Failed to save image')->persistent('Dismiss');
        }
        
        return back();
    }
    public function uploadValidId(Request $request,$id)
    {
        // dd($request->all());
        $customer = Client::findOrfail($id);
        $customer->valid_id = $request->valid_id_type;
        $customer->valid_id_number = $request->id_number;

        $attachment = $request->file('id_file');
        $original_name = $attachment->getClientOriginalName();
        $name = time().'_'.$attachment->getClientOriginalName();
        $attachment->move(public_path().'/valid_ids/', $name);
        $file_name = '/valid_ids/'.$name;

        $customer->valid_file = $file_name;
        $customer->save();

        Alert::success('Successfully Uploaded')->persistent('Dismiss');
        return back();
    }
    public function contractSign(Request $request,$id)
    {
        // dd($request->all());

        $customer = Client::findOrfail($id);

        $attachment = $request->file('contract_signature');
        $original_name = $attachment->getClientOriginalName();
        $name = time().'_'.$attachment->getClientOriginalName();
        $attachment->move(public_path().'/signatures/', $name);
        $file_name = '/signatures/'.$name;
        $customer->signature = $file_name;

        $customer->save();

        Alert::success('Successfully Uploaded')->persistent('Dismiss');
       return redirect()->to('view-client/' . $customer->id);
    }

  public function getUser($id)
{
   $serials = Stove::where('serial_number', 'like', '%' . $id . '%')->first();
   if($serials)
   {
   $client = Client::findOrfail($serials->client_id);
    $user = User::find($client->user_id);

    if ($user) {
        return response()->json([
            'success' => true,
            'user' => [
                'id' => $client->id,
                'name' => $user->name
            ]
        ]);
    } else {
        return response()->json(['success' => false], 404);
    }
       }
       else

       {
         return response()->json(['success' => false], 404);
       }
       
}
    public function sign($id)
    {
        $customer = Client::findOrfail($id);

        return view('signature',
        array(
        'customer' => $customer
        ));
    }

    public function regions()
    {
        try {
            return response()->json($this->psgcGet('regions'));
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }

    public function provinces($region)
    {
        try {
            $regionCode = $this->resolvePsgcCode('regions', $region);

            return response()->json($this->psgcGet("regions/{$regionCode}/provinces"));
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }

    public function regionCities($region)
    {
        try {
            $regionCode = $this->resolvePsgcCode('regions', $region);

            return response()->json($this->psgcGet("regions/{$regionCode}/cities-municipalities"));
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }

    public function cities($province)
    {
        try {
            $provinceCode = $this->resolvePsgcCode('provinces', $province);

            return response()->json($this->psgcGet("provinces/{$provinceCode}/cities-municipalities"));
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }

    public function barangays($city)
    {
        try {
            $cityCode = $this->resolvePsgcCode('cities-municipalities', $city);

            return response()->json($this->psgcGet("cities-municipalities/{$cityCode}/barangays"));
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }

    private function psgcGet($path)
    {
        $client = new GuzzleClient([
            'base_uri' => 'https://psgc.cloud/api/',
            'timeout' => 10,
        ]);

        $response = $client->get($path);

        return json_decode($response->getBody()->getContents(), true);
    }

    private function resolvePsgcCode($endpoint, $value)
    {
        if (preg_match('/^\d+$/', $value)) {
            return $value;
        }

        $normalize = function ($text) {
            $text = preg_replace('/\s+/', ' ', trim((string) $text));
            $text = preg_replace('/^(city|municipality)\s+of\s+/i', '', $text);

            return mb_strtolower($text);
        };

        $normalizedValue = $normalize($value);
        $items = $this->psgcGet($endpoint);
        $match = collect($items)->first(function ($item) use ($normalizedValue, $normalize) {
            return $normalize($item['name'] ?? '') === $normalizedValue;
        });

        return $match['code'] ?? $value;
    }
}
