var interestRate = 6.75;
var tenor = 5 * 12;
var maxDSR = 60 / 100;
var defaultFutureValue = 0;
var totalOfPeriods = 12;
var investIndustryType =  {
  'Professionals': 15.18,
  'Services': 15.18,
  'Logistics & Transportation': 14.65,
  'Food & Bev': 10.7,
  'Wholesale': 9.99,
  'Import & Export': 9.99,
  'Trading': 9.99,
  'Manufacturing': 8.41,
  'Construction ': 5.64,
  'Retail': 4.11
};

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
  if ($("#monthly-turnover").val() == "") {
    toastr.warning("Please enter your company monthly turnover.");
    $("#monthly-turnover").focus();
    return false;
  }

  if ($("#monthly-commmitement").val() == "") {
    toastr.warning("Please enter your monthly commmitement.");
    $("#monthly-commmitement").focus();
    return false;
  }

  var prop_type = $('select[name="industry_type"]').val();
  if(!prop_type)
  {
    toastr.warning("Please choose type of industry");
    $("#priv_prop").focus();
    return false;
  }

  return true;
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
  while (txt.length>digit)
  {
    new_txt = separator + txt.substring(txt.length - digit,txt.length) + new_txt;
    txt = txt.substring(0,txt.length - digit);
  }
  new_txt = txt.substring(0) + new_txt;

  return new_txt+"."+decimalPart;
}

function monthlyCommmitement() {
  return getNumber($('#monthly-commmitement').val());
}

function caculateMonthlyIncome() {
  var monthlyTurnover = getNumber($('#monthly-turnover').val());
  var industryType =  $('select[name="industry_type"]').val();
  var incomeFactor = investIndustryType[industryType];
  return Number((monthlyTurnover * incomeFactor * 1.2 / 100).toFixed(2));
}

function caculateLoanAmount() {
  var installmentAmount = Number((( maxDSR * caculateMonthlyIncome() ) - monthlyCommmitement()).toFixed(2));
  var loanAmount = PV(interestRate, totalOfPeriods, tenor, installmentAmount * -1, defaultFutureValue);
  return loanAmount;
}

function installmentMonthlyAmount() {
  var loanAmount = getNumber($('#loan-amount').val());
  return PMT(defaultFutureValue, loanAmount * -1, (interestRate * 0.01) / 12, tenor, 0);
}

function caculateAdjustedCommitement() {
  return (monthlyCommmitement() + installmentMonthlyAmount());
}

function caculateDSR() {
  return Number(( (caculateAdjustedCommitement() / caculateMonthlyIncome()) * 100).toFixed(2));
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

function caculateMonthlyCommmitement(){
  return ( getNumber($('#business-term-loan').val()) + getNumber($('#mortgage-loan').val()) + getNumber($('#purchase-loan').val()) + getNumber($('#private-lender-loan').val()) );
}

function resetAllMonthlyIndividualCommitment(){
  $('#business-term-loan').val("");
  $('#mortgage-loan').val("");
  $('#purchase-loan').val("");
  $('#private-lender-loan').val("");
}

$('#monthly-commmitement').on('keyup', function(){
  resetAllMonthlyIndividualCommitment();
});

$('#business-term-loan, #mortgage-loan, #purchase-loan, #private-lender-loan').on('keyup', function(){
  var summaryCommitement = caculateMonthlyCommmitement();
  $('#monthly-commmitement').val(summaryCommitement);
});

$("#submit-reset").click(function(){
  $("#monthly-turnover").val("");
  $("#monthly-commmitement").val("");
  $("#business-term-loan").val("");
  $("#mortgage-loan").val("");
  $("#purchase-loan").val("");
  $("#private-lender-loan").val("");
  $('#industry_type').val("");
  $('#loan-amount').val("");
});

$('#monthly-commmitement-individual').click(function(){
  $('#monthly-commmitement-body').slideToggle(function() {
    visible = $("#monthly-commmitement-body").is(":visible");
    $("#monthly-commmitement-individual i").toggleClass('fa-plus', !visible);
    $("#monthly-commmitement-individual i").toggleClass('fa-minus', visible);
  });
});

$("#submit-prepayment").click(function(){
  stat = validateInputs();
  if(stat) {
    var DSR = caculateDSR();
    var loanAmount = $('#loan-amount').val();
    if (DSR > 60){
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

    loanAmount = caculateLoanAmount();
    $('#loan-amount').val(addSeparator(loanAmount, 3));
    $("html, body").animate({"scrollTop":$(".loan-amount-result").offset().top},800);
  }
});

$("#submit-back").click(function(){
  $("#repayDet").hide();
  $("#personalInfo").fadeIn('5000');
  $("html, body").animate({"scrollTop":$("#stps").offset().top},800);
  $("#stps .step-2").removeClass('active');
});