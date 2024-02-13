<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\ItemInvoice;
use App\Models\AccessMenu;
use App\Models\AccessSub;
use App\Models\AccessSubChild;
use App\Models\Menu;
use App\Models\MenuSub;
use App\Models\MenuSubsChild;
use App\Models\UserRole;

use App\Models\FinancialTransaction;

class CustomerController extends Controller
{
    public function showCustIndex(Request $request)
    {     
        $user = Auth::user();
        if (!$user) {
            // Handle ketika user tidak ditemukan (sesuai kebutuhan Anda)
            // Contoh: Redirect ke halaman login
            return redirect('/login');
        }
        // dd($user);

        $accessMenus = AccessMenu::where('user_id', $user->role)->pluck('menu_id');
        $accessSubmenus = AccessSub::where('role_id', $user->role)->pluck('submenu_id');
        $accessChildren = AccessSubChild::where('role_id', $user->role)->pluck('childsubmenu_id');
    
        // Mengambil data berdasarkan hak akses
        $menus = Menu::whereIn('id', $accessMenus)->get();
        $subMenus = MenuSub::whereIn('id', $accessSubmenus)->get();
        $childSubMenus = MenuSubsChild::whereIn('id', $accessChildren)->get();

        $roleData = UserRole::where('id', $user->role)->first();

        $customers = Customer::all();
        $additionalData = [
            'title'                 => 'Custumer',
            'subtitle'              => 'List',
            'customers'             => $customers,
            'menus'                 => $menus,
            'subMenus'              => $subMenus,
            'childSubMenus'         => $childSubMenus,
            'user'                  => $user,
            'role'                  => $roleData,
            'individualCount'       => Customer::countIndividualCustomers(),
            'individualToday'       => Customer::todayIndividual('individual'),
            'individualPercn'       => Customer::individualPecentage('individual'),
            'biroCustomerCount'     => Customer::countBiroCustomers(),
            'biroToday'             => Customer::todayIndividual('biro'),
            'biroPercn'             => Customer::individualPecentage('biro'),
            'instansiCount'         => Customer::countInstansiCustomers(),            
            'instToday'             => Customer::todayIndividual('instansi'),
            'instPercn'             => Customer::individualPecentage('instansi'),
            'inActiveCustumer'      => Customer::countInactiveCustomer(),
        ];   
        return view('Konten/Custumer/list', $additionalData);
    }

    public function getAllCustomers()
    {
        try {
            $customers = Customer::all();            
            $customers->each(function ($customer) {

                $totalSpent = Invoice::where('customer_uuid', $customer->uuid)->sum('total_amount');
                $customer->total_spent = $totalSpent;
    
                $totalOrders = Invoice::where('customer_uuid', $customer->uuid)->count();
                $customer->total_orders = $totalOrders;

                $lastMonth = Carbon::now()->subMonth(1);
                $hasOrderLastMonth = Invoice::where('customer_uuid', $customer->uuid)
                    ->where('created_at', '>=', $lastMonth)
                    ->exists();

                $customer->active = $hasOrderLastMonth ? 'Active' : 'Inactive';
                dd($hasOrderLastMonth);
            });
    
            return response()->json(['data' => $customers]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getCustomerByUUID($uuid)
    {
        $customer = Customer::where('uuid', $uuid)->first();

        if (!$customer) {
            return response()->json(['error' => 'Customer not found'], 404);
        }

        return response()->json(['data' => $customer]);
    }
    
    public function addCustomer(Request $request)
    {
        try {
            $request->validate([
                'name'          => 'required',
                'phone'         => 'required|numeric',
                'email'         => 'nullable|email',
            ]);

            $data = [
                'name'          => $request->input('name'),
                'email'         => $request->input('email'),
                'phone'         => $request->input('phone'),
                'country'       => $request->input('country'),
                'address'       => $request->input('address'),
                'customer_type' => $request->input('customer_type'),
                'country_code'  => $request->input('country_code'),
            ];

            $newCustomer = Customer::createCustomer($data);

            if ($newCustomer) {
                $response = [
                    'success' => true,
                    'title' => 'Berhasil',
                    'message' => 'Customer berhasil ditambahkan.'
                ];              

                return redirect()->route('customer.list')->with('response', $response);
            } else {
                throw new \Exception('Gagal menambahkan customer.');
            }
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'title' => 'Gagal',
                'message' => $e->getMessage()
            ];
            return redirect()->back()->with('response', $response);
        }
    }

    public function editcustomer(Request $request)
    {
        // Validate the request data
        $request->validate([
            'name' => 'required',
            'phone' => 'required',
            'email' => 'email',
            'address' => 'string',
            'uuid' => 'required',
        ]);

        // Get the data from the request
        $name = $request->input('name');
        $phone = $request->input('phone');
        $email = $request->input('email');
        $address = $request->input('address');
        $uuid = $request->input('uuid');

        // Find the customer by UUID
        $customer = Customer::where('uuid', $uuid)->first();

        // Update the customer data
        if ($customer) {
            $customer->name = $name;
            $customer->phone = $phone;
            $customer->email = $email;
            $customer->address = $address;
            $customer->save();
        
            $response = [
                'success' => true,
                'title' => 'Berhasil',
                'message' => 'Data Customer berhasil dirubah.',
            ];
        
            return redirect()->back()->with('response', $response);
        } else {
            $response = [
                'success' => false,
                'title' => 'Gagal',
                'message' => 'Perubahan Data Gagal.',
            ];
        
            return redirect()->back()->with('response', $response);
        }
    }

    public function deleteCustomer(Request $request)
    {
        $uuid = $request->input('uuid');

        try {
            // Find the customer
            $customer = Customer::where('uuid', $uuid)->first();

            if (!$customer) {
                throw new \Exception('Customer not found');
            }

            // Get all invoices associated with the customer
            $invoices = Invoice::where('customer_uuid', $uuid)->get();

            $deletedItemTotalCount = 0;
            $deletedInvoiceTotalCount = 0;
            $deletedTransactionTotalCount = 0;

            foreach ($invoices as $invoice) {
                $invoiceNumber = $invoice->invoice_number;

                // Delete related items
                $deletedItemCount = ItemInvoice::where('invoice_id', $invoiceNumber)->delete();
                $deletedItemTotalCount += $deletedItemCount;

                // Delete the invoice
                $deletedInvoiceCount = Invoice::where('invoice_number', $invoiceNumber)->delete();
                $deletedInvoiceTotalCount += $deletedInvoiceCount;

                // Delete related financial transactions
                $deletedTransactionCount = FinancialTransaction::where('reference_number', $invoiceNumber)->delete();
                $deletedTransactionTotalCount += $deletedTransactionCount;
            }

            // Delete the customer
            $customer->delete();

            $deletedItemsInfo = $deletedItemTotalCount > 0 ? "$deletedItemTotalCount items" : "No Items";
            $deletedInvoiceInfo = $deletedInvoiceTotalCount > 0 ? "$deletedInvoiceTotalCount Invoice" : "No Invoice";
            $deletedTransactionInfo = $deletedTransactionTotalCount > 0 ? "$deletedTransactionTotalCount Transactions" : "No Transactions";

            $successMessage = " Data lain yang dihapus: $deletedInvoiceInfo, $deletedItemsInfo, $deletedTransactionInfo";

            $response = [
                'success' => true,
                'title' => "Berhasil",
                'message' => "Customer '{$customer->name}' deleted successfully.{$successMessage}",
            
            ];

            return redirect()->route('customer.list')->with('response', $response);
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'title' => "Gagal",
                'message' => $e->getMessage(),
            ];
            return redirect()->back()->with('response', $response);
        }
    }   
}
