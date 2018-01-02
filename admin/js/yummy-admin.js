(function($) {
  'use strict';

  /**
   * All of the code for your admin-facing JavaScript source
   * should reside in this file.
   *
   * Note: It has been assumed you will write jQuery code here, so the
   * $ function reference has been prepared for usage within the scope
   * of this function.
   *
   * This enables you to define handlers, for when the DOM is ready:
   *
   * $(function() {
   *
   * });
   *
   * When the window is loaded:
   *
   * $( window ).load(function() {
   *
   * });
   *
   * ...and/or other possibilities.
   *
   * Ideally, it is not considered best practise to attach more than a
   * single DOM-ready or window-load handler for a particular page.
   * Although scripts in the WordPress core, Plugins and Themes may be
   * practising this, we should strive to set a better example in our own work.
   */


  $(document).ready(function() {
    var yummyApp = document.querySelector('#scheduler_here');

    if (yummyApp) {
      initScheduler();
    }
  });

})(jQuery);




function initScheduler() {
  console.log("initScheduler!");
  scheduler.config.xml_date = "%Y-%m-%d %H:%i";
  scheduler.config.prevent_cache = true;
  scheduler.config.first_hour = 11;
  scheduler.config.last_hour = 24;
  scheduler.config.time_step = 15;
  scheduler.config.limit_time_select = true;
  scheduler.locale.labels.section_location = "Location";
  scheduler.config.details_on_create = true;
  scheduler.config.details_on_dblclick = true;
  scheduler.config.prevent_cache = true;

  scheduler.locale.labels.section_yummy_guests_number = "Ospiti";
  scheduler.locale.labels.section_yummy_user_name = "Nome";
  scheduler.locale.labels.section_yummy_user_lastname = "Cognome";
  scheduler.locale.labels.section_yummy_user_email = "email";
  scheduler.locale.labels.section_yummy_user_telephone = "Telefono";
  scheduler.locale.labels.section_time = "Orario";






  scheduler.config.lightbox.sections = [
    // {name:"description", height:130, map_to:"text", type:"textarea" , focus:true},
    //{name:"location", height:43, type:"textarea", map_to:"details" },
    // {name:"yummy_guests_number", height:43, type:"textarea", map_to:"yummy_guests_number" },

    // {name:"yummy_user_name", height:33, type:"textarea", map_to:"yummy_user_name" },
    // {name:"yummy_user_lastname", height:33, type:"textarea", map_to:"yummy_user_lastname" },



    // {name:"yummy_guests_number", height:43, type:"select", map_to:"yummy_guests_number", options:[
    //   {key:"1", label:"1"},
    //   {key:"2", label:"2"},
    //   {key:"3", label:"3"},
    //   {key:"4", label:"4"}
    // ]},




    // {name:"time", height:72, type:"time", map_to:"auto"}
  ];



  getUsers();





  for (var i = 0; i < wpApiSettingsAdmin.yummy_booking_post_meta.length; i++) {
    yummy_booking_post_meta = wpApiSettingsAdmin.yummy_booking_post_meta[i];
    if ((yummy_booking_post_meta != "yummy_order_start_date") && (yummy_booking_post_meta != "yummy_order_end_date") && (yummy_booking_post_meta != "yummy_guests_number")) {
      scheduler.config.lightbox.sections.push({
        name: yummy_booking_post_meta,
        height: 33,
        type: "textarea",
        map_to: yummy_booking_post_meta
      });
    }
  }

  scheduler.config.lightbox.sections.push({
    name: "yummy_guests_number",
    height: 43,
    type: "select",
    map_to: "yummy_guests_number",
    options: [{
        key: "1",
        label: "1"
      },
      {
        key: "2",
        label: "2"
      },
      {
        key: "3",
        label: "3"
      },
      {
        key: "4",
        label: "4"
      },
      {
        key: "5",
        label: "5"
      }
    ]
  });


  scheduler.config.lightbox.sections.push({
    name: "content",
    height: 130,
    map_to: "content",
    type: "textarea",
    focus: true
  });
  scheduler.config.lightbox.sections.push({
    name: "time",
    height: 72,
    type: "time",
    map_to: "auto"
  });



  scheduler.templates.event_class = function(start, end, event) {
    var css = "";

    if (event.subject) // if event has subject property then special class should be assigned
      css += "event_" + event.subject;

    if (event.id == scheduler.getState().select_id) {
      css += " selected";
    }
    return css; // default return

  };






  scheduler.init('scheduler_here', new Date(2017, 11, 18), "week");
  //scheduler.load("http://localhost/dhtmlxScheduler/samples/01_initialization_loading/data/events_json.php", "json");
  //http://localhost/yummy/wp-content/plugins/yummy/scheduler.json
  scheduler.load(wpApiSettingsAdmin.root + "wp/v2/yummy-booking", "json");



  //var dp = new dataProcessor("http://localhost/dhtmlxScheduler/samples/01_initialization_loading/data/events_json.php");
  // var dp = new dataProcessor("http://localhost/yummy/wp-json/wp/v2/yummy-booking?_wpnonce=" + wpApiSettingsAdmin.nonce);
  // dp.init(scheduler);
  // dp.setTransactionMode("REST",true);
  // console.log(dp);




  scheduler.attachEvent("onEventAdded", function(id, ev) {

    // console.log(ev);




    postData = {
      "yummy_order_start_date": ISODateString(ev.start_date),
      "yummy_order_end_date": ISODateString(ev.end_date),
      "yummy_user_name": ev.yummy_user_name,
      "yummy_user_lastname": ev.yummy_user_lastname,
      "yummy_user_email": ev.yummy_user_email,
      "yummy_user_telephone": ev.yummy_user_telephone,

      "yummy_guests_number": ev.yummy_guests_number,

      "title": ev.yummy_user_lastname + " " + ev.yummy_user_name,
      "content": ev.content,
      "status": "publish",
    }



    createPost = new XMLHttpRequest();
    createPost.open('POST', wpApiSettingsAdmin.root + 'wp/v2/yummy-booking');
    createPost.setRequestHeader('X-WP-Nonce', wpApiSettingsAdmin.nonce);
    createPost.setRequestHeader('Content-Type', 'application/json; charset="UTF-8"');

    createPost.send(JSON.stringify(postData));
    createPost.onreadystatechange = function() {
      if (createPost.readyState == 4) {
        if (createPost.status == 201) {
          console.log("data added");
        } else {
          alert("error");

        }
      }
    }


  });


  function ISODateString(d) {
    function pad(n) {
      return n < 10 ? '0' + n : n
    }
    return d.getUTCFullYear() + '-' +
      pad(d.getUTCMonth() + 1) + '-' +
      pad(d.getUTCDate()) + ' ' +
      pad(d.getUTCHours() + 1) + ':' +
      pad(d.getUTCMinutes()) + ''
    // + pad(d.getUTCSeconds())
  }



  // console.log('dp: ' + dp);


}






function getUsers() {
  //console.log(wpApiSettings);
  var output = {};
  var request = new XMLHttpRequest();
  request.open('GET', wpApiSettingsAdmin.root + 'wp/v2/users/?_wpnonce=' + wpApiSettingsAdmin.nonce);
  request.onload = function() {
    if (request.status >= 200 && request.status < 400) {
      data = JSON.parse(request.responseText);

      createUsers(data);

    } else {
      console.log('We connected to the server but it returned an error');
    }
  }

  request.onerror = function() {
    console.log('Connection error');
  }

  request.send();

}


function createUsers(data) {
  var users = [];

  for (var i = 0; i < data.length; i++) {


    users.push({
      key: data[i].name,
      label: data[i].name
    });
  }

	console.log("users: " + users);


  scheduler.config.lightbox.sections.push({
    name: "yummy_users",
    height: 43,
    type: "select",
    map_to: "yummy_users",
    options: users,
		onchange : function(users) { console.log(  this.value);  }
  });

}
