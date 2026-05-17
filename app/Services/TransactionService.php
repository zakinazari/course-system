<?php

namespace App\Services;

use App\Models\Financial\Transaction;
use App\Enums\TransactionCategory;
use App\Enums\Action;
use App\Models\Financial\Account;
use DB;
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
        $category,
        $source_type = null,
        $source_id = null,
        $section_id = null,
        Action $action,
        $note,
    ) {

        DB::beginTransaction();

        try {

            // حساب مبدا
            $from_account = Account::findOrFail($from_account_id);

            // حساب مقصد
            $to_account = Account::findOrFail($to_account_id);

            /*
            |--------------------------------------------------------------------------
            | کم شدن از حساب مبدا
            |--------------------------------------------------------------------------
            */
            Transaction::create([

                'account_id' => $from_account->id,

                'branch_id' => $from_account->branch_id,

                'type' => 'expense',

                'amount' => $amount,

                'category' => $category,

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
            | اضافه شدن به حساب مقصد
            |--------------------------------------------------------------------------
            */
            Transaction::create([

                'account_id' => $to_account->id,

                'branch_id' => $to_account->branch_id,

                'type' => 'income',

                'amount' => $amount,

                'category' => $category,

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
            | update balances
            |--------------------------------------------------------------------------
            */
            $from_account->decrement('balance', $amount);

            $to_account->increment('balance', $amount);

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