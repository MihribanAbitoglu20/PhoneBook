<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Cache;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Validator;

class ContactController extends Controller
{

    public $successStatus = 200;

    public function index(Request $request)
    {
        try {
            $name = $request->name;
            $surname = $request->surname;
            $phone = $request->phone;
            $order = $request->order;

            if($name != Cache::get('name') || $surname != Cache::get('surname') ||
                $phone != Cache::get('phone') || $order != Cache::get('order')){
                Cache::put('name',$name);
                Cache::put('surname',$surname);
                Cache::put('phone',$phone);
                Cache::put('order',$order);
                Cache::forget('contacts');
            }

            $result = Cache::rememberForever('contacts',function() use($name,$surname,$phone,$order){

                $name = Cache::get('name');
                $surname = Cache::get('surname');
                $phone = Cache::get('phone');
                $order = Cache::get('order');

                $contacts = Contact::query();

                if (!is_null($name)) {
                    $contacts->where('contacts.name', 'like', '%' . $name . '%');
                }
                if (!is_null($surname)) {
                    $contacts->where('contacts.surname', 'like', '%' . $surname . '%');
                }
                if (!is_null($phone)) {
                    $contacts->where('contacts.phone', 'like', '%' . $phone . '%');
                }
                if (!is_null($order)) {
                    $contacts->orderBy('name', $order);
                    $contacts->orderBy('surname', $order);
                }

                $contacts->select('contacts.*');
                $contacts = $contacts->get();
                $count = $contacts->count();
                return response()->json(['count' => $count, 'data' => $contacts]);
            });

            return $result;

        } catch (\Exception $exception) {
            return response()->json(['status' => 'error', 'message' => $exception->getMessage()]);
        }

    }

    public function store(Request $request)
    {

        try {
            $validator = Validator::make(request()->all(), [
                'name' => 'required|string|max:255',
                'surname' => 'required|string|max:255',
                'phone' => 'required|string|unique:contacts|max:50'
            ],
                [
                    'name.required' => 'Ki??i ad?? zorunludur!',
                    'name.string' => 'Girilen veri tipi uyumsuz!',
                    'name.max' => 'L??tfen en fazla 255 karakter giriniz!',
                    'surname.required' => 'Ki??i soyad?? zorunludur!',
                    'surname.string' => 'Girilen veri tipi uyumsuz!',
                    'surname.max' => 'L??tfen en fazla 255 karakter giriniz!',
                    'phone.required' => 'Ki??i telefonu zorunludur!',
                    'phone.unique' => 'Ki??i telefonu benzersiz olmal??d??r!',
                    'phone.string' => 'Girilen veri tipi uyumsuz!',
                    'phone.max' => 'L??tfen en fazla 50 karakter giriniz!',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->messages();
                return response()->json(['status' => 'error', 'message' => $messages], 422);
            }

            $contact = new Contact();
            $contact->name = $request->name;
            $contact->surname = $request->surname;
            $contact->phone = $request->phone;
            $contact->save();
            Cache::forget('contacts');
            return response()->json([
                'status' => 'success',
                'message' => 'Ki??i ba??ar??yla eklendi.'], $this->successStatus);
        } catch (\Exception $exception) {
            return response()->json(['status' => 'error', 'message' => $exception->getMessage()]);
        }


    }

    public function update(Request $request, $id)
    {

        try {

            $contact = Contact::find($id);

            if ($contact) {
                $validator = Validator::make(request()->all(), [
                    'name' => 'required|string|max:255',
                    'surname' => 'required|string|max:255',
                    'phone' => 'required|string|unique:contacts,phone,' . $id . '|max:50'
                ],
                    [
                        'name.required' => 'Ki??i ad?? zorunludur!',
                        'name.string' => 'Girilen veri tipi uyumsuz!',
                        'name.max' => 'L??tfen en fazla 255 karakter giriniz!',
                        'surname.required' => 'Ki??i soyad?? zorunludur!',
                        'surname.string' => 'Girilen veri tipi uyumsuz!',
                        'surname.max' => 'L??tfen en fazla 255 karakter giriniz!',
                        'phone.required' => 'Ki??i telefonu zorunludur!',
                        'phone.unique' => 'Ki??i telefonu benzersiz olmal??d??r!',
                        'phone.string' => 'Girilen veri tipi uyumsuz!',
                        'phone.max' => 'L??tfen en fazla 50 karakter giriniz!',
                    ]
                );

                if ($validator->fails()) {
                    $messages = $validator->messages();
                    return response()->json(['status' => 'error', 'message' => $messages], 422);
                }


                $contact->name = $request->name;
                $contact->surname = $request->surname;
                $contact->phone = $request->phone;
                $contact->update();
                Cache::forget('contacts');
                return response()->json([
                    'status' => 'success',
                    'message' => 'Ki??i ba??ar??yla g??ncellendi.'
                ], $this->successStatus);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Ki??i kayd?? bulunamad??!'], 500);
            }

        } catch (\Exception $exception) {
            return response()->json(['status' => 'error', 'message' => $exception->getMessage()]);
        }

    }

    public function destroy($id)
    {
        try {
            $contact = Contact::find($id);
            if ($contact) {
                $contact->delete();
                Cache::forget('contacts');
                return response()->json([
                    'status' => 'success',
                    'message' => 'Ki??i ba??ar??yla silindi.'
                ], $this->successStatus);
            } else {
                return response()->json([
                    'status' => 'error', 'message' => 'Ki??i kayd?? bulunamad??!'], 500);
            }
        } catch (\Exception $exception) {
            return response()->json(['status'=>'error','message' => $exception->getMessage()]);
        }

    }
}
