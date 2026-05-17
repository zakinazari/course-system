<?php

namespace App\Enums;

enum TransactionCategory: string
{
    case COURSE_FEE = 'course_fee';
    case OTHER_FEE = 'other_fee';
    case EXAM_FINE = 'exam_fine';
    case BOOK_SALE = 'book_sale';
    case EXPENSE = 'expense';
    case ASSET = 'asset';
    case SALARY_ADVANCE = 'salary_advance';
    case SALARY_ADVANCE_SETTLEMENT = 'salary_advance_settlement';
    
    case PERMANENT_SALARY_PAYMENT = 'permanent_salary_payment';
    case TEMPORARY_SALARY_PAYMENT = 'temporary_salary_payment';

    case CORRECTION = 'correction';
    
    case BOOK_PURCHASE = 'book_purchase';

    case OPENING_BALANCE = 'opening_balance';
    case CAPITAL_INJECTION = 'capital_injection';
    case LOAN_RECEIVED = 'loan_received';
}