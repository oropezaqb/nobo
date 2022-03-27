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
                $permission21 = new Permission([
                    'key' => 'browse_reviewed_vouchers',
                    'table_name' => 'reviewed_vouchers',
                ]);
                $permission21->save();
                $permission22 = new Permission([
                    'key' => 'read_reviewed_vouchers',
                    'table_name' => 'reviewed_vouchers',
                ]);
                $permission22->save();
                $permission23 = new Permission([
                    'key' => 'edit_reviewed_vouchers',
                    'table_name' => 'reviewed_vouchers',
                ]);
                $permission23->save();
                $permission24 = new Permission([
                    'key' => 'add_reviewed_vouchers',
                    'table_name' => 'reviewed_vouchers',
                ]);
                $permission24->save();
                $permission25 = new Permission([
                    'key' => 'delete_reviewed_vouchers',
                    'table_name' => 'reviewed_vouchers',
                ]);
                $permission25->save();
                $role5 = Role::where('name', 'fsup_ga')->firstOrFail();
                $role5->permissions()->save($permission21);
                $role5->permissions()->save($permission22);
                $role5->permissions()->save($permission23);
                $role5->permissions()->save($permission24);
                $role5->permissions()->save($permission25);
                */
                $permission26 = new Permission([
                    'key' => 'browse_approved_vouchers',
                    'table_name' => 'approved_vouchers',
                ]);
                $permission26->save();
                $permission27 = new Permission([
                    'key' => 'read_approved_vouchers',
                    'table_name' => 'approved_vouchers',
                ]);
                $permission27->save();
                $permission28 = new Permission([
                    'key' => 'edit_approved_vouchers',
                    'table_name' => 'approved_vouchers',
                ]);
                $permission28->save();
                $permission29 = new Permission([
                    'key' => 'add_approved_vouchers',
                    'table_name' => 'approved_vouchers',
                ]);
                $permission29->save();
                $permission30 = new Permission([
                    'key' => 'delete_approved_vouchers',
                    'table_name' => 'approved_vouchers',
                ]);
                $permission30->save();
                $role6 = Role::where('name', 'fsup_ga')->firstOrFail();
                $role6->permissions()->save($permission26);
                $role6->permissions()->save($permission27);
                $role6->permissions()->save($permission28);
                $role6->permissions()->save($permission29);
                $role6->permissions()->save($permission30);
                $permission31 = new Permission([
                    'key' => 'browse_bank_endorsements',
                    'table_name' => 'bank_endorsements',
                ]);
                $permission31->save();
                $permission32 = new Permission([
                    'key' => 'read_bank_endorsements',
                    'table_name' => 'bank_endorsements',
                ]);
                $permission32->save();
                $permission33 = new Permission([
                    'key' => 'edit_bank_endorsements',
                    'table_name' => 'bank_endorsements',
                ]);
                $permission33->save();
                $permission34 = new Permission([
                    'key' => 'add_bank_endorsements',
                    'table_name' => 'bank_endorsements',
                ]);
                $permission34->save();
                $permission35 = new Permission([
                    'key' => 'delete_bank_endorsements',
                    'table_name' => 'bank_endorsements',
                ]);
                $permission35->save();
                $role7 = new Role([
                    'name' => 'fsup_treasury',
                    'display_name' => 'Finance Supervisor - Treasury',
                ]);
                $role7->save();
                $role7->permissions()->save($permission31);
                $role7->permissions()->save($permission32);
                $role7->permissions()->save($permission33);
                $role7->permissions()->save($permission34);
                $role7->permissions()->save($permission35);
                $permission36 = new Permission([
                    'key' => 'browse_payments',
                    'table_name' => 'payments',
                ]);
                $permission36->save();
                $permission37 = new Permission([
                    'key' => 'read_payments',
                    'table_name' => 'payments',
                ]);
                $permission37->save();
                $permission38 = new Permission([
                    'key' => 'edit_payments',
                    'table_name' => 'payments',
                ]);
                $permission38->save();
                $permission39 = new Permission([
                    'key' => 'add_payments',
                    'table_name' => 'payments',
                ]);
                $permission39->save();
                $permission40 = new Permission([
                    'key' => 'delete_payments',
                    'table_name' => 'payments',
                ]);
                $permission40->save();
                $role8 = Role::where('name', 'fa_disbursements')->firstOrFail();
                $role8->permissions()->save($permission36);
                $role8->permissions()->save($permission37);
                $role8->permissions()->save($permission38);
                $role8->permissions()->save($permission39);
                $role8->permissions()->save($permission40);
            });
            return redirect(route('dashboard'));
        } catch (\Exception $e) {
            return back()->with('status', $this->translateError($e))->withInput();
        }
    }
}
