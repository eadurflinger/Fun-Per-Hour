var dat = {
    data: {
        set: {
            'username' : 'USERNAME',
            'password' : 'PASSWORD',
            'adminname' : "NAME",
            'adminemail' : "EMAIL",
        }
    },
};



$( document ).ready(function() {
    
    $('#login_button').click(function(e){
        e.preventDefault();
        console.log('clicked');
        $.ajax({
          type: 'POST',
          url: 'http://fun-per-hour.herokuapp.com/API/v1/newUser',
          contentType: 'application/json',
          dataType: 'json',
          data: JSON.stringify(dat),
          success: (result,status,xhr) => {
            console.log(result);
            console.log(`${JSON.stringify(xhr)} ${status}`);
          },
          error: (xhr, status, error) => {
            console.log('err');
            console.log (`${JSON.stringify(xhr)} ${status}`);
          }
        });
    });
    
});
