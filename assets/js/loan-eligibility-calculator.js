var interestRate = 6.75;
var tenor = 5 * 12;
var maxDSR = 60 / 100;
var defaultFutureValue = 0;
var totalOfPeriods = 12;

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

  if ($("#monthly-commitment").val() == "") {
    toastr.warning("Please enter your monthly commitment.");
    $("#monthly-commitment").focus();
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
  return _.find(investIndustryType, function(industry){
    return industry.industry == value
  });
}

function caculateMonthlyIncome() {
  var monthlyTurnover = getNumber($('#monthly-turnover').val());
  var industryType =  $('select[name="industry_type"]').val();
  var incomeFactor = getNumber(getIncomeFactor(industryType).income_factor);
  return Number((monthlyTurnover * incomeFactor * 1.2 / 100).toFixed(2));
}

function caculateLoanAmount() {
  var installmentAmount = Number((( maxDSR * caculateMonthlyIncome() ) - monthlyCommitment()).toFixed(2));
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
  var html_clone = $('.tab-content').find('.tab-pane').last().clone();

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
  $('.nav-tabs li:nth-child(' + id + ') a').click();
});