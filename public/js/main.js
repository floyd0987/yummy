// console.log(wpApiSettings);


var yummyOutput = document.querySelector('#yummy-output');
var yummyButton = document.querySelector('#yummy-button');

var yummyUserOutput = document.querySelector('#yummy-user-output');

if (yummyOutput) {
  yummyOutput.innerHTML = "Please wait...";
  getPosts();
  getUser();
}

function getFormData() {

  var title = "Prenotazione: " + document.querySelector('input[name="yummy_user_lastname"]').value;
  var content = document.querySelector('textarea[name="yummy-content"]').value;
  // var yummy_order_date = document.querySelector('input[name="yummy_order_date"]').value;




  // var meta = {
  //   'yummy_order_date': 'sgagasgags',
  //   'test2': 'asgasg'
  // };

  var postData = {
    "title": title,
    "content": content,
    "status": "publish",
    //"yummy_order_date": yummy_order_date,
    //"meta" : meta
  };



  for (var i = 0; i < wpApiSettings.yummy_booking_post_meta.length; i++) {
    yummy_booking_post_meta = wpApiSettings.yummy_booking_post_meta[i];

    postData[yummy_booking_post_meta] = document.querySelector('input[name="' + yummy_booking_post_meta + '"]').value;


  }

  console.log(postData);

  return postData;

}

yummyButton.addEventListener("click", function() {

  postData = getFormData();


  // console.log( JSON.stringify(postData));



  createPost = new XMLHttpRequest();
  createPost.open('POST', wpApiSettings.root + 'wp/v2/yummy-booking');
  createPost.setRequestHeader('X-WP-Nonce', wpApiSettings.nonce);
  createPost.setRequestHeader('Content-Type', 'application/json; charset="UTF-8"');




  createPost.send(JSON.stringify(postData));
  createPost.onreadystatechange = function() {
    if (createPost.readyState == 4) {
      if (createPost.status == 201) {
        getPosts();
      } else {
        alert("error");
        console.log(createPost);
      }
    }
  }



});




function getPosts() {
  var output = {};
  var request = new XMLHttpRequest();
  request.open('GET', wpApiSettings.root + 'wp/v2/yummy-booking');
  request.onload = function() {
    if (request.status >= 200 && request.status < 400) {
      data = JSON.parse(request.responseText);

      createHTML(data);
    } else {
      console.log('We connected to the server but it returned an error');
    }
  }

  request.onerror = function() {
    console.log('Connection error');
  }

  request.send();

}


function createHTML(data) {
  console.log(data);
  var output = '';
  for (var i = 0; i < data.length; i++) {

    output += '<h2>' + data[i].title + '</h2>';
    output += '<div class="yummy-content">' + data[i].content + '</div>';
    //output += '<div class="yummy-order-date">Data: ' + data[i].yummy_order_date + '</div>';


    for (var key in data[i].meta) {
      if (data[i].meta.hasOwnProperty(key)) {
        if (wpApiSettings.yummy_booking_post_meta.indexOf(key) != -1) {
          output += '<div class="yummy-order-date">' + key + ': ' + data[i].meta[key] + '</div>';
        }
      }
    }

  }

  if (yummyOutput) {
    yummyOutput.innerHTML = output;
  }

}




function getUser() {
  //console.log(wpApiSettings);
  var output = {};
  var request = new XMLHttpRequest();
  request.open('GET', wpApiSettings.root + 'wp/v2/users/me?_wpnonce=' + wpApiSettings.nonce);
  request.onload = function() {
    if (request.status >= 200 && request.status < 400) {
      data = JSON.parse(request.responseText);

      createUserHTML(data);
      //console.log(data);

    } else {
      console.log('We connected to the server but it returned an error');
    }
  }

  request.onerror = function() {
    console.log('Connection error');
  }

  request.send();

}


function createUserHTML(data) {
  console.log(data);
  var output = '';
  // for (var i = 0; i < data.length; i++) {
  //   console.log(data[i]);
  //   output += '<h2>Welcome' + data[i].name + '</h2>';
  //
  // }
  document.querySelector('#yummy_user_name').value = data.meta.first_name;
  document.querySelector('#yummy_user_lastname').value = data.meta.last_name;
  document.querySelector('#yummy_user_email').value = data.user_email;

  output += '<h2>Welcome ' + data.name + '</h2>';

  if (yummyUserOutput) {
    yummyUserOutput.innerHTML = output;
  }

}
