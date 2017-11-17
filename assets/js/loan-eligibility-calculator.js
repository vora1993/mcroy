var interestRate = 6.75;
var tenor = 5 * 12;
var maxDSR = 60 / 100;
var defaultFutureValue = 0;
var totalOfPeriods = 12;
if (!_.isEmpty(incomeFactorAddons.base_average_noa_compare)) {
  var baseAverageNOACompare = incomeFactorAddons.base_average_noa_compare.value_compare;
  var averageNOARate = incomeFactorAddons.base_average_noa_compare.income_factor_rate;
} else {
  var baseAverageNOACompare = 30000;
  var averageNOARate = 0.75;
}

if (!_.isEmpty(incomeFactorAddons.base_net_profit_compare)) {
  var baseNetProfitCompare = incomeFactorAddons.base_net_profit_compare.value_compare;
  var netProfitRate = incomeFactorAddons.base_net_profit_compare.income_factor_rate;
} else {
  var baseNetProfitCompare = 100000;
  var netProfitRate = 0.5;
}

if (!_.isEmpty(incomeFactorAddons.base_annual_depreciation_compare)) {
  var baseAnnualDepreciationCompare = incomeFactorAddons.base_annual_depreciation_compare.value_compare;
  var annualDepreciationRate = incomeFactorAddons.base_annual_depreciation_compare.income_factor_rate;
} else {
  var baseAnnualDepreciationCompare = 70000;
  var annualDepreciationRate = 0.5;
}

if (!_.isEmpty(incomeFactorAddons.base_interest_expense_compare)) {
  var baseInterestExpenseCompare = incomeFactorAddons.base_interest_expense_compare.value_compare;
  var interestExpenseRate = incomeFactorAddons.base_interest_expense_compare.income_factor_rate;
} else {
  var baseInterestExpenseCompare = 50000;
  var interestExpenseRate = 0.5;
}

var AutoNumericOptions = { currencySymbol : '$', minimumValue: 0, modifyValueOnWheel: false };

function enter_check(e)
{
  var e=window.event || e;
  var keyunicode=e.charCode || e.keyCode;
  if (keyunicode==13)
  {
    if(!validateInputs())
    {
      return false;
    }
  }
  return true;
}

function validateInputs() {
  var passed = true;
  $.each($('#business-loan-eligibility input:visible'), function(){
    var $this = $(this);
    if ($this.attr('type') == 'text'){
      var attr = $this.attr('required');
      var required = typeof attr !== typeof undefined && attr !== false;
      if ($this.val() == "" && required ) {
        toastr.warning($this.attr('data-message'));
        $this.focus();
        passed = false;
        return false;
      }
    }
  });
  return passed;
}

function PMT(fv, pv, rate, nper, type)
{
  return((-fv - pv * Math.pow(1 + rate, nper)) / ((1 / rate + type) * (Math.pow(1 + rate, nper) - 1)));
}

function PV(rate, per, nper, pmt, fv)
{
  nper = parseFloat(nper);
  pmt = parseFloat(pmt);
  fv = parseFloat(fv);
  rate = eval((rate)/(per * 100));

  if (( pmt == 0 ) || ( nper == 0 )) {
    alert("Why do you want to test me with zeros?");
    return(0);
  }

  if ( rate == 0 ) // Interest rate is 0
  {
    pv_value = -(fv + (pmt * nper));
  } else {
    x = Math.pow(1 + rate, -nper);
    y = Math.pow(1 + rate, nper);
    pv_value = - ( x * ( fv * rate - pmt + y * pmt )) / rate;
  }
  pv_value = conv_number(pv_value, 2);
  return (pv_value);
}

function conv_number(expr, decplaces)
{
  var str = "" + Math.round(eval(expr) * Math.pow(10,decplaces));
  while (str.length <= decplaces) {
    str = "0" + str;
  }
  var decpoint = str.length - decplaces;

  return (str.substring(0,decpoint) + "." + str.substring(decpoint,str.length));
}

function addSeparator(valueAmt, digit)
{
  var separator = ",";
  var new_txt = "";
  var flag = 0;
  var decimalPart = String(valueAmt).split(".")[1];

  if (decimalPart == null)
  {
    decimalPart = "00";

  }
  else
  {
    if (decimalPart.length == 1)
    {
      decimalPart +=  "0";
    }
  }
  var txt = String(valueAmt).split(".")[0];
  var substr = txt;
  /////////////////////////////////////////////////////
  do
  {
    substr = txt.substring(0,1);
    if (substr=="0")
    {
      txt = txt.slice(1,txt.length);
    }
    else if (substr != "0")
    {
      flag = 1;
      if (txt.length == 0)
      {
        txt = "0";
      }
    }
  } while (flag == 0);
  while (txt.length > digit)
  {
    new_txt = separator + txt.substring(txt.length - digit,txt.length) + new_txt;
    txt = txt.substring(0,txt.length - digit);
  }
  new_txt = txt.substring(0) + new_txt;

  return new_txt + "." + decimalPart;
}

function monthlyCommitment() {
  return getNumber($('#monthly-commitment').val());
}

function getIncomeFactor(value){
  return _.find(incomeFactorAddons.income_factor, function(industry){
    return industry.industry == value
  });
}

function getNetProfit() {
  return getNumber($('#company-net-profit').val());
}

function getAnnualDepreciation() {
  return getNumber($('#annual-depreciation').val());
}

function getAnnualInterest() {
  return getNumber($('#annual-interest').val());
}

function caculateMonthlyIncome() {
  var monthlyTurnover = getNumber($('#monthly-turnover').val());
  var incomeFactor = caculateIncomeFactor();
  return Number((monthlyTurnover * incomeFactor * 1.2 / 100).toFixed(2));
}

function caculateIncomeFactor(){
  var industryType =  $('select[name="industry_type"]').val();
  var incomeFactor = getNumber(getIncomeFactor(industryType).income_factor);

  // Base on directors’ information
  if (caculateAverageNOA() > baseAverageNOACompare) {
    incomeFactor += 0.8;
  } else {
    incomeFactor -= 0.5;
  }

  // Base on Financial Information
  if (getNetProfit() >= baseNetProfitCompare) {
    incomeFactor += 0.75;
  } else {
    incomeFactor += 0.5;
  }

  if (getAnnualDepreciation() > baseAnnualDepreciationCompare) {
    incomeFactor -= 0.75;
  } else {
    incomeFactor -= 0.5;
  }

  if (getAnnualInterest() > baseInterestExpenseCompare) {
    incomeFactor -= 0.75;
  } else {
    incomeFactor += 0.5;
  }

  return incomeFactor;
}

function caculateLoanAmount() {
  var installmentAmount = Number((( maxDSR * caculateMonthlyIncome() ) - monthlyCommitment()).toFixed(2));
  console.log(installmentAmount);
  var loanAmount = PV(interestRate, totalOfPeriods, tenor, installmentAmount * -1, defaultFutureValue);
  return loanAmount;
}

function installmentMonthlyAmount() {
  var loanAmount = getNumber($('#loan-amount').val());
  return PMT(defaultFutureValue, loanAmount * -1, (interestRate * 0.01) / 12, tenor, 0);
}

function caculateAdjustedCommitment() {
  return (monthlyCommitment() + installmentMonthlyAmount());
}

function caculateDSR() {
  return Number(( (caculateAdjustedCommitment() / caculateMonthlyIncome()) * 100).toFixed(2));
}

function caculateAverageNOA() {
  var sumComputeNOA = 0;
  $.each($('input[id*="_noa_type_"]'), function(){
    var $this = $(this);
    sumComputeNOA += getNumber($this.val());
  });
  return Number((sumComputeNOA / $('input[id*="_noa_type_"]').length).toFixed(2));
}

function getNumber(value){
  var number = ( typeof( value ) !== "undefined" && value != "" ? value : 0 ).toString().replace(/[^\d.]/ig, '');
  return parseFloat(number)
}

function collectData(){
  $.ajax({
    method: "POST",
    url: base_path + '/page/business-loan-eligibility-calculator',
    data: $('#business-loan-eligibility').serialize(),
  })
}

function caculateMonthlyCommitment(){
  return ( getNumber($('#business-term-loan').val()) + getNumber($('#mortgage-loan').val()) + getNumber($('#purchase-loan').val()) + getNumber($('#private-lender-loan').val()) );
}

function resetAllMonthlyIndividualCommitment(){
  $('#business-term-loan').val("");
  $('#mortgage-loan').val("");
  $('#purchase-loan').val("");
  $('#private-lender-loan').val("");
}

$('#monthly-commitment').on('keyup', function(){
  resetAllMonthlyIndividualCommitment();
});

$('#business-term-loan, #mortgage-loan, #purchase-loan, #private-lender-loan').on('keyup', function(){
  var summaryCommitment = caculateMonthlyCommitment();
  $('#monthly-commitment').val(summaryCommitment);
});

$('input[name="premises_type"]').on('change', function(){
  if ($(this).val() == 'rented'){
    $('#rented-section-box').removeClass('hidden');
  } else {
    $('#rental-amount').val("");
    $('#rented-section-box').addClass('hidden');
  }
});

$('body').on('keyup change', '.director-noa', function() {
  var averageNOA = caculateAverageNOA();
  $('#total-average-all-directors').text("$" + addSeparator(averageNOA, 3));
});

$('body').on('change', '.local-property input[type="radio"]', function(){
  if ($(this).val() == 'yes'){
    $(this).closest('.form-group').next().removeClass('hidden');
  } else {
    $('#property_owned_type').val("");
    $(this).closest('.form-group').next().addClass('hidden');
  }
});

$("#submit-reset").click(function(){
  $("#business-loan-eligibility input[type='text']").val("");
});

$('#monthly-commitment-individual').click(function(){
  $('#monthly-commitment-body').slideToggle(function() {
    visible = $("#monthly-commitment-body").is(":visible");
    $("#monthly-commitment-individual i").toggleClass('fa-plus', !visible);
    $("#monthly-commitment-individual i").toggleClass('fa-minus', visible);
  });
});

$("#submit-prepayment").click(function(){
  stat = validateInputs();
  if(stat) {
    var loanAmount = caculateLoanAmount();
    if (loanAmount < 0){
      return swal({
        html: "<p>Can't get loan, would you like our consultant to contact you for alternative funding?</p><br/><small><strong>*disclaimer</strong>: Note that above serves as a general guideline and other factors like key man credit bureau, financial statement analysis, etc will be taken into consideration for bank approval criteria.</small>",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#8dc63f',
        cancelButtonColor: '#525e64',
        confirmButtonText: 'Ok',
        cancelButtonText: 'Cancel',
        confirmButtonClass: 'btn green-custom btn-lg',
        cancelButtonClass: 'btn grey-mint font-white btn-lg margin-top-sm',
      }).then(function () {
        window.location.href = base_path + '/page/contact-us';
      });
    } else if (loanAmount !== "" ) {
      collectData();
      return swal({
        html: "<p>Congratulation! Based on our MoneyCompare Algorithm, you are liable for S$" + addSeparator(loanAmount, 3) + " loan amount.</p><p>Apply through <a href='http://moneycompare.sg/'>MoneyCompare.SG</a>, we will send you an email on the approving bank names!</p>",
        type: 'success',
        showCancelButton: true,
        confirmButtonColor: '#8dc63f',
        cancelButtonColor: '#525e64',
        confirmButtonText: 'Apply Myself!',
        cancelButtonText: 'Cancel',
        confirmButtonClass: 'btn green-custom btn-lg',
        cancelButtonClass: 'btn grey-mint font-white btn-lg margin-top-sm',
      }).then(function () {
        window.location.href = base_path + '/loan-application/business-loan/business-term-loan';
      });
    }
  }
});

$(".nav-tabs").on("click", "a", function (e) {
  e.preventDefault();
  if (!$(this).hasClass('add-applicant')) {
    $(this).tab('show');
  }
})

$('.add-applicant').click(function (e) {
  e.preventDefault();
  var id = $(".nav-tabs").children().length; //think about it ;)
  var tabId = 'applicant_' + id;
  $(this).closest('li').before('<li><a href="#applicant_' + id + '">Director ' + id + '</a></li>');
  var html_clone = $('.tab-content').find('.tab-pane').last().clone(true);

  $.each(html_clone.find(":input"), function() {
    var thisid = $(this).attr('id');
    thisid = thisid.replace(/\d+/, id);
    var thisname = $(this).attr('name');
    thisname = thisname.replace(/\d+/, id);

    $(this).attr('name', thisname);
    $(this).attr('id', thisid);
    if ($(this).attr('type') == 'text'){
      $(this).val('');
    }
  });

  $.each(html_clone.find("label"), function() {
    var thisfor = $(this).attr('for');
    if (thisfor && (thisfor !== '') ){
      thisfor = thisfor.replace(/\d+/, id);
      $(this).attr('for', thisfor);
    }
  });

  html_clone.find('.director-property-box').removeClass('hidden');
  $('.tab-content').append('<div class="tab-pane panel-body" id="' + tabId + '">' + html_clone.html() + '</div>');
  AutoNumeric.multiple( '#' + tabId + ' input[type="text"]', AutoNumericOptions);
  $('.nav-tabs li:nth-child(' + id + ') a').click();
});

AutoNumeric.multiple('.page-eligibility input[type="text"]', AutoNumericOptions);
