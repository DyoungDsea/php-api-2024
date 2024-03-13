
<!DOCTYPE html>
<html lang="en">
   
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">  
    <link rel="icon" href="{ICON}" type="image/x-icon">
    <link rel="shortcut icon" href="{ICON}" type="image/x-icon">
    <title>{SITE_NAME}</title>
    <link href="https://fonts.googleapis.com/css?family=Work+Sans:100,200,300,400,500,600,700,800,900" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Poppins:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <style type="text/css">
      body{
      width: 650px;
      font-family: work-Sans, sans-serif;
      background-color: #f6f7fb;
      display: block;
      padding: 20px 0;
      }
      a{
      text-decoration: none;
      }
      span {
      font-size: 14px;
      }
      p {
          font-size: 13px;
         line-height: 1.7;
         letter-spacing: 0.7px;
         margin-top: 0;
      }
      .text-center{
      text-align: center
      }

      .button{ margin: 20px 0; }

      .btn{
        background-color: #043423;
        color: white;
        padding: 10px 20px;
        border-radius: 20px;
      }

    </style>
  </head>
  <body style="margin: 30px auto;">
    <table style="width: 100%">
      <tbody>
        <tr>
          <td>
            <table style="background-color: #f6f7fb; width: 100%">
              <tbody>
                <tr>
                  <td>
                     
                    <table style="width: 650px; margin: 0 auto; background-color: #fff; border-radius: 8px">
                      <tbody>
                       
                        <tr>
                          <td style="padding: 30px"> 
                            <center> 
                            <img src="{LOGO}" style="max-width:200px" style="width:200px ;" alt="">
                            </center>
                            <hr>
                            <p>Dear {NAME},</p>
                            <p>Welcome to {SITE_NAME}, we're glad to have you here, you need to verify your account by clicking on the verify me button below</p>
                            <div class="text-center button">
                              <a class="btn" href="{SITE_ADDR}/verify?id={USERID}&key={KEY}">Verify Me</a>
                            </div>
                            <p style="margin-bottom: 0">Good luck!</p>
                            <hr>
                            <p style="color:#00acef;text-align: center"><b>Powered By {SITE_NAME} </b></p>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                     
                  </td>
                </tr>
              </tbody>
            </table>
          </td>
        </tr>
      </tbody>
    </table>
  </body>
 
</html>