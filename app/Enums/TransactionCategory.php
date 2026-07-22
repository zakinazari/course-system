<?php

namespace App\Enums;

enum TransactionCategory: string
{
    // ======================================================
    // OPERATIONAL INCOME (Daily business income)
    // ======================================================

    case COURSE_FEE = 'course_fee';

    case OTHER_FEE = 'other_fee';

    case EXAM_FINE = 'exam_fine';

    case BOOK_SALE = 'book_sale';

    case MAKEUP_FEE = 'makeup_fee';


    // ======================================================
    // OPERATIONAL EXPENSE (Daily business expense)
    // ======================================================

    case EXPENSE = 'expense';

    case ASSET = 'asset';

    case SALARY_ADVANCE = 'salary_advance';

    case PERMANENT_SALARY_PAYMENT = 'permanent_salary_payment';

    case TEMPORARY_SALARY_PAYMENT = 'temporary_salary_payment';

    case BOOK_PURCHASE = 'book_purchase';

    case SECURITY_SAVING_REFUND = 'security_saving_refund';

    // ======================================================
    // TREASURY / CAPITAL / NON-OPERATIONAL
    // ======================================================

    case OPENING_BALANCE = 'opening_balance';

    case CAPITAL_INJECTION = 'capital_injection';

    case LOAN_RECEIVED = 'loan_received';


    // ======================================================
    // SYSTEM / INTERNAL / ADJUSTMENTS
    // ======================================================
    
    case SALARY_ADVANCE_SETTLEMENT = 'salary_advance_settlement';

    case CORRECTION = 'correction';

    case ACCOUNT_TRANSFER = 'account_transfer';



    public static function incomeCategories(): array
    {
        return [
            self::COURSE_FEE,
            self::OTHER_FEE,
            self::EXAM_FINE,
            self::BOOK_SALE,
            self::MAKEUP_FEE,
        ];
    }

    public static function expenseCategories(): array
    {
        return [
            self::EXPENSE,
            self::ASSET,
            self::SALARY_ADVANCE,
            self::PERMANENT_SALARY_PAYMENT,
            self::TEMPORARY_SALARY_PAYMENT,
            self::BOOK_PURCHASE,
            self::SECURITY_SAVING_REFUND,
        ];
    }
}