var interestRate = 6.75;
var tenor = 5;
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
  var prop_type = $('input[type="radio"][name="industry_type"]:checked').val();
  if(!prop_type)
  {
    toastr.warning("Please choose type of industry");
    $("#priv_prop").focus();
    return false;
  }
  if ($("#monthly-turnover").val() == "") {
    toastr.warning("Please enter your company monthly turnover.");
    $("#monthly-turnover").focus();
    return false;
  }

  return true;
}

function PMT(fv, pv, rate, nper, type)
{
  return((-fv - pv * Math.pow(1 + rate, nper)) / ((1 / rate + type) * (Math.pow(1 + rate, nper) - 1)));
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

function caculateMonthlyCommmitement() {
  var hirePurchaseLoan = getNumber($('#purchase_loan').val());
  var propertyLoan = getNumber($('#property_loan').val());
  var businessTermLoan = getNumber($('#business_term_loan').val());
  var othersLoan = getNumber($('#others').val());

  return hirePurchaseLoan + propertyLoan + businessTermLoan + othersLoan;
}

function caculateMonthlyIncome() {
  var monthlyTurnover = getNumber($('#monthly_turnover').val());
  console.log(monthlyTurnover);
  var industryType =  $('input[type="radio"][name="industry_type"]:checked').val();
  console.log(industryType);
  var incomeFactor = investIndustryType[industryType];
  console.log(incomeFactor);
  return Number((monthlyTurnover * incomeFactor).toFixed(2));
}

function getNumber(value){
  console.log(value);
  return ( typeof( value )!=="undefined" ? value : 0 ).toString().replace(/[^\d.]/ig, '');
}

$("#submit-reset").click(function(){
  $("#monthly-turnover").val("");
  $("#purchase_loan").val("");
  $("#property_loan").val("");
  $("#business_term_loan").val("");
  $("#others").val("");
  $('input[name="industry_type"]').prop('checked', false);
});

$("#submit-prepayment").click(function(){
  stat = validateInputs();
  if(stat) {
    var monthlyCommitement = caculateMonthlyCommmitement();
    var grossMonthlyIncome = caculateMonthlyIncome();
    console.log(grossMonthlyIncome);
    // $("html, body").animate({"scrollTop":$(".repayWrap").offset().top},800);
  }
});

$("#submit-back").click(function(){
  $("#repayDet").hide();
  $("#personalInfo").fadeIn('5000');
  $("html, body").animate({"scrollTop":$("#stps").offset().top},800);
  $("#stps .step-2").removeClass('active');
})

$(window,document).load(function(){

});