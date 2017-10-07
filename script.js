'use strict';

let dat = {
  data: {
    set: {
        'username' : 'USERNAME',
        'password' : 'PASSWORD',
        'adminname' : "NAME",
        'adminemail' : "EMAIL",
    }
  },
  newRand: '0',
};

let res;

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

console.log(res);
