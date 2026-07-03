<?php

namespace App\Http\Controllers;
use RealRashid\SweetAlert\Facades\Alert;
use App\User;
use App\Dealer;
use App\Center;
use App\TransactionDetail;
use App\Item;
use App\DmsArea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class DealerController extends Controller
{
    //
    public function index(Request $request)
    {
        $activeDealers = Dealer::where('status', 'Active')->count();
        $inactiveDealers = Dealer::where('status', 'Inactive')->count();
        $items = Item::select('item')->get(); // master list of items
        
        $centers = Center::get();
        $dealers = Dealer::with('user')->get();

        $areas = $this->dealerAreaOptions();
        return view('dealers',
            array(
                'dealers' => $dealers,
                'activeDealers' => $activeDealers,
                'inactiveDealers' => $inactiveDealers,
                'items' => $items,
                'centers' => $centers,
                'areas' => $areas

            )
        );
    }

    public function megaDealers(Request $request)
    {
        $activeDealers = Dealer::where('status', 'Active')
            ->whereHas('user', function ($q) {
                $q->where('role', 'Mega Dealer');
            })
            ->count();

        $inactiveDealers = Dealer::where('status', 'Inactive')
            ->whereHas('user', function ($q) {
                $q->where('role', 'Mega Dealer');
            })
            ->count();

        $items = Item::select('item')->get();
        $centers = Center::get();
        $areas = $this->dealerAreaOptions();
        $dealers = Dealer::with(['user', 'orders', 'sales'])
            ->whereHas('user', function ($q) {
                $q->where('role', 'Mega Dealer');
            })
            ->get();

        return view('dealers', [
            'dealers' => $dealers,
            'activeDealers' => $activeDealers,
            'inactiveDealers' => $inactiveDealers,
            'items' => $items,
            'centers' => $centers,
            'areas' => $areas,
            'dealerPageTitle' => 'Mega Dealers',
            'dealerSingularTitle' => 'Mega Dealer',
            'dealerRouteName' => 'mds',
        ]);
    }
    
    public function show(Request $request)
    {
        return view('dashboard-dealer');
    }

    public function newDealer(Request $request)
    {
        $isAdmin = auth()->user()->role === 'Admin';
        $dealerType = $isAdmin && strtolower((string) $request->dealer_type) === 'regular'
            ? 'Regular'
            : 'Project';

        $request->validate([
            'dealer_type' => $isAdmin ? 'required|in:Project,Regular' : 'nullable',
            'spo' => $dealerType === 'Project' ? 'required|string|max:255' : 'nullable',
            'center' => $dealerType === 'Project' ? 'required|string|max:255' : 'nullable',
        ]);

        if ($this->dealerDuplicateExists(
            $request->first_name,
            $request->last_name,
            $request->mothers_name
        )) {
            $message = "Dealer with same First Name, Last Name, and Mother's Name already exists.";

            Alert::error('Duplicate dealer', $message)->persistent('Dismiss');

            return redirect()->back()
                ->withErrors(['dealer_duplicate' => $message])
                ->withInput();
        }

        $fullName = trim(collect([
            $request->first_name,
            $request->middle_name,
            $request->last_name,
        ])->filter()->implode(' '));

        $user = new User;
        $user->name = $fullName;
        $user->first_name = $request->first_name;
        $user->middle_name = $request->middle_name;
        $user->last_name = $request->last_name;
        $user->mothers_name = $request->mothers_name;
        $user->email = $request->email_address;
        $user->role = 'Dealer';
        $user->birthdate = $request->birthdate;
        $user->age = $request->age;
        $user->password = bcrypt('12345678');
        $user->save();

        $dealer = new Dealer;
        $dealer->user_id = $user->id;
        $dealer->dealer_reference = $this->nextDealerReference($dealerType);
        $dealer->name = $fullName;
        if (Schema::hasColumn('dealers', 'dealer_type')) {
            $dealer->dealer_type = $dealerType;
        }
        $dealer->spo = $dealerType === 'Project' ? $request->spo : null;
        $dealer->email_address = $request->email_address;
        $dealer->number = $request->number;
        $dealer->facebook = $request->facebook;
        $dealer->address = $request->address;
        $dealer->location_region = $request->location_region;
        $dealer->location_province = $request->location_province;
        $dealer->location_city = $request->location_city;
        $dealer->location_barangay = $request->location_barangay;
        if (Schema::hasColumn('dealers', 'postal_code')) {
            $dealer->postal_code = $request->postal_code;
        }
        if (Schema::hasColumn('dealers', 'street_address')) {
            $dealer->street_address = $request->street_address;
        }
        $dealer->store_name = $request->store_name;
        $dealer->store_type = $request->store_type;
        $dealer->center = $dealerType === 'Project' ? $request->center : null;
        $dealer->area = $request->area;
        $dealer->latitude = $request->latitude;
        $dealer->longitude = $request->longitude;
        $dealer->status = "Active";
        $dealer->save();
        

        Alert::success('Successfully encoded')->persistent('Dismiss');
        return redirect('view-dealer/' . $dealer->id);
    }

    public function checkDuplicate(Request $request)
    {
        $exists = $this->dealerDuplicateExists(
            $request->first_name,
            $request->last_name,
            $request->mothers_name
        );

        return response()->json([
            'exists' => $exists,
            'message' => $exists
                ? "Dealer with same First Name, Last Name, and Mother's Name already exists."
                : null,
        ]);
    }

    private function dealerDuplicateExists($firstName, $lastName, $mothersName)
    {
        $firstName = trim((string) $firstName);
        $lastName = trim((string) $lastName);
        $mothersName = trim((string) $mothersName);

        if (!$firstName || !$lastName || !$mothersName) {
            return false;
        }

        return User::where('role', 'Dealer')
            ->whereRaw('LOWER(TRIM(first_name)) = ?', [mb_strtolower($firstName)])
            ->whereRaw('LOWER(TRIM(last_name)) = ?', [mb_strtolower($lastName)])
            ->whereRaw('LOWER(TRIM(mothers_name)) = ?', [mb_strtolower($mothersName)])
            ->exists();
    }

    private function dealerAreaOptions()
    {
        return DmsArea::with('areaAd.distributor')
            ->whereNotNull('name')
            ->orderBy('name')
            ->get();
    }

    public function view(Request $request,$id)
    {
        $dealer = Dealer::with('user')->findOrfail($id);
        $transactions = TransactionDetail::where('dealer_id',$dealer->user_id)->orderBy('id','desc')->get();
        $centers = Center::get();
        $areas = $this->dealerAreaOptions();
        // dd($dealer);
        return view('dealer',
            array(
                'dealer' => $dealer,
                'transactions' => $transactions,
                'centers' => $centers,
                'areas' => $areas,
            )
        );
    }
    public function changeAvatar(Request $request, $id)
    {
        $dealer = Dealer::findOrfail($id);
        
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
        
        $directory = public_path('avatar-dealer');
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }
        
        $fileName = 'avatar_dealer_' . $dealer->id . '_' . time() . '.png';
        $filePath = $directory . '/' . $fileName;
        
        if (file_put_contents($filePath, $imageData)) {
            if ($dealer->avatar && 
                $dealer->avatar !== url('design/assets/images/profile/user-1.png') && 
                file_exists(public_path(str_replace(url('/'), '', $dealer->avatar)))) {
                unlink(public_path(str_replace(url('/'), '', $dealer->avatar)));
            }
            
            $dealer->avatar = 'avatar-dealer/' . $fileName;
            $dealer->save();
            
            Alert::success('Successfully Uploaded')->persistent('Dismiss');
        } else {
            Alert::error('Failed to save image')->persistent('Dismiss');
        }
        
        return back();
    }

    public function uploadValidId(Request $request,$id)
    {
        // dd($request->all());
        $customer = Dealer::findOrfail($id);
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

        $customer = Dealer::findOrfail($id);

        $attachment = $request->file('contract_signature');
        $original_name = $attachment->getClientOriginalName();
        $name = time().'_'.$attachment->getClientOriginalName();
        $attachment->move(public_path().'/signatures/', $name);
        $file_name = '/signatures/'.$name;
        $customer->signature = $file_name;

        $customer->save();

        Alert::success('Successfully Uploaded')->persistent('Dismiss');
        return redirect()->to('view-dealer/' . $customer->id);
    }

    public function sign($id)
    {
        $dealer = Dealer::findOrfail($id);

        return view('signature_dealer',
        array(
        'dealer' => $dealer
        ));
    }

    public function update(Request $request, $id)
    {
        $dealer = Dealer::findOrFail($id);
        $isAdmin = auth()->user()->role === 'Admin';
        $existingDealerType = Schema::hasColumn('dealers', 'dealer_type')
            ? ($dealer->dealer_type ?: 'Project')
            : 'Project';
        $dealerType = $isAdmin
            ? (strtolower((string) $request->dealer_type) === 'regular' ? 'Regular' : 'Project')
            : $existingDealerType;

        $request->validate([
            'dealer_type' => $isAdmin ? 'required|in:Project,Regular' : 'nullable',
            'spo' => $dealerType === 'Project' ? 'required|string|max:255' : 'nullable',
            'center' => $dealerType === 'Project' ? 'required|string|max:255' : 'nullable',
        ]);

        $fullName = trim(collect([
            $request->first_name,
            $request->middle_name,
            $request->last_name,
        ])->filter()->implode(' '));

        if ($dealer->user_id) {

            User::where('id', $dealer->user_id)->update([
                'name' => $fullName ?: $request->name,
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'last_name' => $request->last_name,
            ]);
        }

        $dealer->name = $fullName ?: $request->name;
        $dealer->number = $request->number;
        $dealer->address = $request->address;
        $dealer->street_address = $request->street_address;
        $dealer->store_name = $request->store_name;
        $dealer->store_type = $request->store_type;
        if ($isAdmin && strcasecmp($existingDealerType, $dealerType) !== 0) {
            $dealer->dealer_reference = $this->nextDealerReference($dealerType);
        }
        if (Schema::hasColumn('dealers', 'dealer_type')) {
            $dealer->dealer_type = $dealerType;
        }
        $dealer->spo = $dealerType === 'Project' ? $request->spo : null;
        $dealer->facebook = $request->facebook;
        $dealer->email_address = $request->email_address;
        $dealer->location_region = $request->location_region;
        $dealer->location_province = $request->location_province;
        $dealer->location_city = $request->location_city;
        $dealer->location_barangay = $request->location_barangay;
        $dealer->postal_code = $request->postal_code;
        $dealer->center = $dealerType === 'Project' ? $request->center : null;
        $dealer->area = $request->area;
        $dealer->latitude = $request->latitude;
        $dealer->longitude = $request->longitude;

        $dealer->save();

        Alert::success('Success', 'Dealer updated successfully!');
        return redirect()->back();
    }

    private function nextDealerReference($dealerType)
    {
        if (strcasecmp((string) $dealerType, 'Regular') === 0) {
            $year = date('Y');
            $prefix = 'DL' . $year;
            $padding = 4;
        } else {
            $prefix = 'PRD';
            $padding = 5;
        }

        $latestSequence = Dealer::where('dealer_reference', 'like', $prefix . '%')
            ->pluck('dealer_reference')
            ->map(function ($reference) use ($prefix) {
                $suffix = substr(strtoupper(trim((string) $reference)), strlen($prefix));

                return ctype_digit($suffix) ? (int) $suffix : 0;
            })
            ->max() ?: 0;

        return $prefix . str_pad($latestSequence + 1, $padding, '0', STR_PAD_LEFT);
    }

    public function getZipCode1(Request $request)
    {
        $lat = $request->latitude;
        $lng = $request->longitude;

        if (!$lat || !$lng) {
            return response()->json([
                'success' => false,
                'zipcode' => null,
                'message' => 'Missing coordinates'
            ]);
        }

        try {
            $client = new \GuzzleHttp\Client([
                'timeout' => 10
            ]);

            $response = $client->get('https://nominatim.openstreetmap.org/reverse', [
                'headers' => [
                    'User-Agent' => 'LaravelApp/1.0 (zipcode lookup)'
                ],
                'query' => [
                    'format' => 'json',
                    'lat' => $lat,
                    'lon' => $lng,
                    'addressdetails' => 1
                ]
            ]);

            $data = json_decode($response->getBody(), true);

            $zipcode = null;

            if (!empty($data['address'])) {
                $address = $data['address'];

                $zipcode =
                    $address['postcode']
                    ?? $address['postal_code']
                    ?? $address['zip']
                    ?? null;
            }

            return response()->json([
                'success' => true,
                'zipcode' => $zipcode
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'zipcode' => null,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function ncrCities()
    {
        $cities = City::where('region', 'NCR')
            ->orderBy('name')
            ->get(['name']);

        return response()->json($cities);
    }
}
