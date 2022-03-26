<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Permission;
use App\Models\Role;

class SetupController extends Controller
{
    public function store(Request $request)
    {
        try {
            \DB::transaction(function () use ($request) {
/*                $permission1 = new Permission([
                    'key' => 'browse_payees',
                    'table_name' => 'payees',
                ]);
                $permission1->save();
                $permission2 = new Permission([
                    'key' => 'read_payees',
                    'table_name' => 'payees',
                ]);
                $permission2->save();
                $permission3 = new Permission([
                    'key' => 'edit_payees',
                    'table_name' => 'payees',
                ]);
                $permission3->save();
                $permission4 = new Permission([
                    'key' => 'add_payees',
                    'table_name' => 'payees',
                ]);
                $permission4->save();
                $permission5 = new Permission([
                    'key' => 'delete_payees',
                    'table_name' => 'payees',
                ]);
                $permission5->save();
                $role1 = new Role([
                    'name' => 'fa_disbursements',
                    'display_name' => 'Finance Analyst - Disbursements',
                ]);
                $role1->save();
                $role1->permissions()->save($permission1);
                $role1->permissions()->save($permission2);
                $role1->permissions()->save($permission3);
                $role1->permissions()->save($permission4);
                $role1->permissions()->save($permission5);
                $permission6 = new Permission([
                    'key' => 'browse_queries',
                    'table_name' => 'queries',
                ]);
                $permission6->save();
                $permission7 = new Permission([
                    'key' => 'read_queries',
                    'table_name' => 'queries',
                ]);
                $permission7->save();
                $permission8 = new Permission([
                    'key' => 'edit_queries',
                    'table_name' => 'queries',
                ]);
                $permission8->save();
                $permission9 = new Permission([
                    'key' => 'add_queries',
                    'table_name' => 'queries',
                ]);
                $permission9->save();
                $permission10 = new Permission([
                    'key' => 'delete_queries',
                    'table_name' => 'queries',
                ]);
                $permission10->save();
                $role2 = new Role([
                    'name' => 'fsup_ga',
                    'display_name' => 'Finance Supervisor - General Accounting',
                ]);
                $role2->save();
                $role2->permissions()->save($permission6);
                $role2->permissions()->save($permission7);
                $role2->permissions()->save($permission8);
                $role2->permissions()->save($permission9);
                $role2->permissions()->save($permission10);
                $permission11 = new Permission([
                    'key' => 'browse_bills',
                    'table_name' => 'bills',
                ]);
                $permission11->save();
                $permission12 = new Permission([
                    'key' => 'read_bills',
                    'table_name' => 'bills',
                ]);
                $permission12->save();
                $permission13 = new Permission([
                    'key' => 'edit_bills',
                    'table_name' => 'bills',
                ]);
                $permission13->save();
                $permission14 = new Permission([
                    'key' => 'add_bills',
                    'table_name' => 'bills',
                ]);
                $permission14->save();
                $permission15 = new Permission([
                    'key' => 'delete_bills',
                    'table_name' => 'bills',
                ]);
                $permission15->save();
                $role3 = Role::where('name', 'fa_disbursements')->firstOrFail();
                $role3->permissions()->save($permission11);
                $role3->permissions()->save($permission12);
                $role3->permissions()->save($permission13);
                $role3->permissions()->save($permission14);
                $role3->permissions()->save($permission15);
*/
                $permission16 = new Permission([
                    'key' => 'browse_vouchers',
                    'table_name' => 'vouchers',
                ]);
                $permission16->save();
                $permission17 = new Permission([
                    'key' => 'read_vouchers',
                    'table_name' => 'vouchers',
                ]);
                $permission17->save();
                $permission18 = new Permission([
                    'key' => 'edit_vouchers',
                    'table_name' => 'vouchers',
                ]);
                $permission18->save();
                $permission19 = new Permission([
                    'key' => 'add_vouchers',
                    'table_name' => 'vouchers',
                ]);
                $permission19->save();
                $permission20 = new Permission([
                    'key' => 'delete_vouchers',
                    'table_name' => 'vouchers',
                ]);
                $permission20->save();
                $role4 = new Role([
                    'name' => 'fa_ap',
                    'display_name' => 'Finance Analyst - Accounts Payable',
                ]);
                $role4->save();
                $role4->permissions()->save($permission16);
                $role4->permissions()->save($permission17);
                $role4->permissions()->save($permission18);
                $role4->permissions()->save($permission19);
                $role4->permissions()->save($permission20);
            });
            return redirect(route('dashboard'));
        } catch (\Exception $e) {
            return back()->with('status', $this->translateError($e))->withInput();
        }
    }
}
