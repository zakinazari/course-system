<?php

namespace App\Services;

use App\Models\Financial\Transaction;
use App\Enums\TransactionCategory;
use App\Enums\Action;
use App\Models\Financial\Account;
use DB;
use Illuminate\Support\Str;
class TransactionService
{
    public static function income(
        $account_id,
        $branch_id,
        $amount,
        $category,
        $source_type = null,
        $source_id = null,
        $section_id = null,
        Action $action,
        $note = null,
    ) {

        DB::beginTransaction();

        try {

            $transaction = Transaction::create([

                'account_id' => $account_id,

                'branch_id' => $branch_id,

                'type' => 'income',

                'amount' => $amount,

                'category' => $category,

                'source_type' => $source_type,

                'source_id' => $source_id,

                'section_id' => $section_id,

                'action' => $action->value,
                
                'note'=>$note,

                'transaction_date' => now()->toDateString(),

                'created_by' => auth()->id(),
            ]);

            /*
            |--------------------------------------------------------------------------
            | update balance
            |--------------------------------------------------------------------------
            */
            Account::where('id', $account_id)
                ->increment('balance', $amount);

            DB::commit();

            return $transaction;

        } catch (\Exception $e) {

            DB::rollBack();

            throw $e;
        }
    }

    public static function expense(
        $account_id,
        $branch_id,
        $amount,
        TransactionCategory $category,
        $source_type = null,
        $source_id = null,
        $section_id = null,
        Action $action,
        $note = null,
    ) {

        DB::beginTransaction();

        try {

            $transaction = Transaction::create([

                'account_id' => $account_id,

                'branch_id' => $branch_id,

                'type' => 'expense',

                'amount' => $amount,

                'category' => $category->value,

                'source_type' => $source_type,

                'source_id' => $source_id,

                'section_id' => $section_id,

                'action' => $action->value,

                'note' => $note,

                'transaction_date' => now()->toDateString(),

                'created_by' => auth()->id(),
            ]);

            /*
            |--------------------------------------------------------------------------
            | update balance
            |--------------------------------------------------------------------------
            */
            Account::where('id', $account_id)
                ->decrement('balance', $amount);

            DB::commit();

            return $transaction;

        } catch (\Exception $e) {

            DB::rollBack();

            throw $e;
        }
    }

    public static function transfer(
        $from_account_id,
        $to_account_id,
        $amount,
        TransactionCategory $category,
        $source_type = null,
        $source_id = null,
        $section_id = null,
        Action $action,
        $note = null,
        $module_type = null,
    )
    {
        DB::beginTransaction();

        try {

            /*
            |--------------------------------------------------------------------------
            | accounts
            |--------------------------------------------------------------------------
            */
            $from_account = Account::findOrFail($from_account_id);

            $to_account = Account::withoutGlobalScopes()->findOrFail($to_account_id);

            /*
            |--------------------------------------------------------------------------
            | same account check
            |--------------------------------------------------------------------------
            */
            if ($from_account->id == $to_account->id) {

                throw new \Exception(
                    'Source and destination accounts cannot be the same'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | insufficient balance
            |--------------------------------------------------------------------------
            */
            if ($from_account->balance < $amount) {

                throw new \Exception(
                    __('label.insufficient_balance')
                );
            }

            /*
            |--------------------------------------------------------------------------
            | transfer group id
            |--------------------------------------------------------------------------
            */
            $transfer_group_id = (string) Str::uuid();

            /*
            |--------------------------------------------------------------------------
            | pending expense transaction
            |--------------------------------------------------------------------------
            */
            $expense_transaction = Transaction::create([

                'account_id' => $from_account->id,

                'branch_id' => $from_account->branch_id,

                'type' => 'expense',

                'amount' => $amount,

                'category' => $category->value,

                'source_type' => $source_type,

                'source_id' => $source_id,

                'section_id' => $section_id,

                'action' => $action->value,

                'note' => $note,

                'status' => 'pending',

                'from_account_id'=> $from_account->id,

                'to_account_id'=> $to_account->id,

                'transfer_group_id' => $transfer_group_id,
                'module_type' => $module_type,

                'transaction_date' => now()->toDateString(),

                'created_by' => auth()->id(),
            ]);

            /*
            |--------------------------------------------------------------------------
            | decrease source account balance
            |--------------------------------------------------------------------------
            */
            $from_account->decrement('balance', $amount);

            DB::commit();

            return $expense_transaction;

        } catch (\Exception $e) {

            DB::rollBack();

            throw $e;
        }
    }

    public static function approveTransfer($transaction_id)
    {
        DB::beginTransaction();

        try {

            /*
            |--------------------------------------------------------------------------
            | pending transfer
            |--------------------------------------------------------------------------
            */
            $expense_transaction = Transaction::withoutGlobalScopes()->findOrFail($transaction_id);

            /*
            |--------------------------------------------------------------------------
            | already approved
            |--------------------------------------------------------------------------
            */
            if ($expense_transaction->status == 'approved') {

                throw new \Exception('Transfer already approved');
            }

            /*
            |--------------------------------------------------------------------------
            | destination account
            |--------------------------------------------------------------------------
            */
            $to_account = Account::findOrFail(
                $expense_transaction->to_account_id
            );

            /*
            |--------------------------------------------------------------------------
            | create income transaction
            |--------------------------------------------------------------------------
            */
            Transaction::create([

                'account_id' => $to_account->id,

                'branch_id' => $to_account->branch_id,

                'type' => 'income',

                'amount' => $expense_transaction->amount,

                'category' => $expense_transaction->category,

                'source_type' => $expense_transaction->source_type,

                'source_id' => $expense_transaction->source_id,

                'section_id' => $expense_transaction->section_id,

                'action' => Action::APPROVE->value,

                'note' => $expense_transaction->note,

                'status' => 'approved',

                'from_account_id'=>$expense_transaction->from_account_id,
                
                'to_account_id'=>$expense_transaction->to_account_id,

                'transfer_group_id' => $expense_transaction->transfer_group_id,
                'module_type' => $expense_transaction->module_type,

                'approved_by' => auth()->id(),

                'approved_at' => now(),

                'transaction_date' => now()->toDateString(),

                'created_by' => auth()->id(),
            ]);

            /*
            |--------------------------------------------------------------------------
            | increase destination balance
            |--------------------------------------------------------------------------
            */
            $to_account->increment(
                'balance',
                $expense_transaction->amount
            );

            /*
            |--------------------------------------------------------------------------
            | update pending transaction
            |--------------------------------------------------------------------------
            */
            $expense_transaction->update([

                'status' => 'approved',

                'approved_by' => auth()->id(),

                'approved_at' => now(),
            ]);

            DB::commit();

            return true;

        } catch (\Exception $e) {

            DB::rollBack();

            throw $e;
        }
    }

    public static function adjust(
        $account_id,
        $type,
        $branch_id,
        $old_amount,
        $new_amount,
        TransactionCategory $category,
        $source_type,
        $source_id,
        $section_id = null,
        $action = null,
        $note = null,
    ) {

        $difference = $new_amount - $old_amount;

        if ($difference == 0) {
            return null;
        }

        // =========================
        // INCOME ADJUSTMENT
        // =========================
        if ($type === 'income') {

            // افزایش درآمد
            if ($difference > 0) {

                return self::income(
                    $account_id,
                    $branch_id,
                    $difference,
                    $category,
                    $source_type,
                    $source_id,
                    $section_id,
                    $action,
                    $note = null,
                );
            }

            // کاهش درآمد
            return self::expense(
                $account_id,
                $branch_id,
                abs($difference),
                TransactionCategory::CORRECTION,
                $source_type,
                $source_id,
                $section_id,
                $action,
                $note = null,
            );
        }

        // =========================
        // EXPENSE ADJUSTMENT
        // =========================
        if ($type === 'expense') {

            // افزایش مصرف
            if ($difference > 0) {

                return self::expense(
                    $account_id,
                    $branch_id,
                    $difference,
                    $category,
                    $source_type,
                    $source_id,
                    $section_id,
                    $action,
                    $note = null,
                );
            }

            // کاهش مصرف
            return self::income(
                $account_id,
                $branch_id,
                abs($difference),
                TransactionCategory::CORRECTION,
                $source_type,
                $source_id,
                $section_id,
                $action,
                $note = null,
            );
        }

        throw new \Exception('Invalid transaction type.');
    }
}