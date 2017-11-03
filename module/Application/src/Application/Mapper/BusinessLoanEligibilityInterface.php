<?php

namespace Application\Mapper;

interface BusinessLoanEligibilityInterface
{
    public function fetchAll($condition);

    public function fetchRow($condition);

    public function insert($business_loan_eligibility);

    public function update($business_loan_eligibility);
}