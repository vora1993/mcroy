    ,----,------------------------------,------.
    | ## |         MoneyCompare         |    - |
    | ## |                              |    - |
    |    |------------------------------|    - |
    |    ||............................||      |
    |    ||,-                        -.||      |
    |    ||___                      ___||    ##|
    |    ||---`--------------------'---||      |
    `----'|_|______________________==__|`------'

# Development

### Installation

1.  `git clone git@github.com:mcroygoh/moneycompare.sg.git && cd moneycompare.sg`
2. Open `config/autoload/global.php` and `config/autoload/local.php` _(fill in your credentials)_
3. Install Docker & Docker Compose ( https://docs.docker.com/install/ - https://docs.docker.com/compose/install/ )
4. `docker-compose up`
5. Access to local server: `http://localhost`

### File Modifications

- Use `module/Backend` folder to modify/add Admin Panel
- Use `module/FrontEnd` folder to modify/add User Page
- Use `module/Application` folder to modify/add Common Function use both Backend & FrontEnd.

### Troubleshooting
- View access log in `data/log/access.log`
- View error log in `data/log/error.log`
- View SQL log in `data/log/sql.log`

# Deploy to staging & production:
#### Prepare
#### Deploy
1. SSH to server: `ssh ubuntu@dev.moneycompare.sg`
2. Go to deploy folder: `cd /var/www/dev.moneycompare.sg`
3. Update new code: `git pull`. We use `develop` branch for deploy STAGING and `master` branch for deploy PRODUCTION.
4. Successed and Ready for Test


# Guide to use:
**I. CORPORATE CREDIT CARD**

- Info:
    + Corporate Credit Card solution help user to find compare best deals on credit cards.

- How it works:
    + Admin add credit card information.
    + Users page display credit cards, filter and compare credit cards base user choose.

- **User Link:** http://dev.moneycompare.sg/credit-cards
- **Admin Panel:**
    + Add/Edit/Delete Credit Cards: http://dev.moneycompare.sg/admin/credit-cards
    + Add/Edit/Delete Credit Card Providers: http://dev.moneycompare.sg/admin/providers

**II. BUSINESS LOAN ELIGIBILITY CALCULATOR**
- Info:
    + Base on what user input, get a quick estimate of how much user could borrow

- How is works:
    + The adjusted commitement + monthly commitement/ income should pass the 60% ratio, if above, system will prompt cant get loan and would u like our consultant to contact u.

    + Bit math involved as we need to reverse calculation the loan amount.

        ```
            (0.6% X Monthly Rev X 0.108 x 1.2) - existing mthly commitment
            (A)=PV(6.75%/12,60,-Payment)
        ```

        See file: [dsr.xlsx](https://trello-attachments.s3.amazonaws.com/59532cfaacdd911fe58da3df/59f04bdfba77d7bc4f414fbb/2121a276faa4af4da6c53d438f9f1bf8/dsr.xlsx)
    + System should generate (A) based on client input of their monthly turnover + monthly commitement with fixed interest amount of 6.75% and fixed 5 years loan tenor which should fufilled the DSR ratio of 60% to determine the loan eligibility amount.

- **User Page Link:** http://dev.moneycompare.sg/page/business-loan-eligibility-calculator/
- **Admin Panel:**
    + Edit income factor http://dev.moneycompare.sg/admin/setting/edit-income-factor
    + Edit  Income Factor - Addon: http://dev.moneycompare.sg/admin/setting/edit-income-factor-addon
    + View List Business Loan Eligibility Calculator: http://dev.moneycompare.sg/admin/business-loan/eligibility-calculator

**III. Update Content Popup Support Info in Admin:**
- Info:
    + Display support popup on Register/SignIn page
- **Admin Panel Link:** http://dev.moneycompare.sg/admin/setting/view-support-info
