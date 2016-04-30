function showad()
    {	    var raw_random = Math.random();
            var raw = Math.ceil(raw_random * 3)
            // set 1
            var ad1 = document.getElementById("ad1");
	    var ad2 = document.getElementById("ad2");
	    var ad3 = document.getElementById("ad3");
            // set 2
            var ad4 = document.getElementById("ad4");
	    var ad5 = document.getElementById("ad5");
	    var ad6 = document.getElementById("ad6");
            // All references will not have 3 sets yet
            var ad7 = document.getElementById("ad7");
            var ad8 = document.getElementById("ad8");
            var ad9 = document.getElementById("ad9");

	    //alert(raw);
            if(raw == 1)
            {
            	ad2.style.display = "none";
                ad3.style.display = "none";
                ad1.style.display = "block";
                ad5.style.display = "none";
                ad6.style.display = "none";
                ad4.style.display = "block";

                if(ad7 != null)
                {
                    ad8.style.display = "none";
                    ad9.style.display = "none";
                    ad7.style.display = "block";  
                }
	    	
	    }
	    
	    if(raw == 2)
	    {
	        ad1.style.display = "none";
	    	ad3.style.display = "none";
                ad2.style.display = "block";
                ad4.style.display = "none";
                ad6.style.display = "none";
                ad5.style.display = "block";
	        
                if(ad7 != null)
                {
                    ad7.style.display = "none";
                    ad9.style.display = "none";
                    ad8.style.display = "block";  
                }
            }

            if(raw == 3)
	    {
	        ad1.style.display = "none";
	    	ad2.style.display = "none";
                ad3.style.display = "block";
                ad4.style.display = "none";
                ad5.style.display = "none";
                ad6.style.display = "block";
	    
                if(ad7 != null)
                {
                    ad7.style.display = "none";
                    ad8.style.display = "none";
                    ad9.style.display = "block";  
                }
            }
     }

// Load the application once the DOM is ready, using `jQuery.ready`:
$(function(){

  //alert('in function');
  var IslandDataObj;


  // IslandData Item View
  // --------------

  // The DOM element for a todo item...
  var IslandDataView = Backbone.View.extend({

	el: "#main-nav",
        
	events: 
        {
	    'click .ads': 'onAdClick',
            'tap .ads': 'onAdClick',
	    'mouseover .ads': 'onAdOver',
            'mouseout .ads': 'onAdOut',
            'click .questions': 'onQuestionClick',
            'tap .questions': 'onQuestionClick',
	    'mouseover .questions': 'onQuestionOver',
            'mouseout .questions': 'onQuestionOut',
            'click .answers': 'onAnswerClick',
            'tap .answers': 'onAnswerClick',
            'mouseover .answers': 'onAnswerOver',
            'mouseout .answers': 'onAnswerOut',
            'click .products': 'onProductClick',
	    'tap .products': 'onProductClick',
	    'mouseover .products': 'onProductOver',
            'mouseout .products': 'onProductOut',
            'click .profiles': 'onProfileClick',
	    'tap .profiles': 'onProfileClick',
	    'mouseover .profiles': 'onProfileOver',
            'mouseout .profiles': 'onProfileOut',
            'click .definitions': 'onDefinitionClick',
	    'tap .definitions': 'onDefinitionClick',
	    'mouseover .definitions': 'onDefinitionOver',
            'mouseout .definitions': 'onDefinitionOut',
            'click .registrations': 'onRegistrationClick',
	    'tap .registrations': 'onRegistrationClick',
	    'mouseover .registrations': 'onRegistrationOver',
            'mouseout .registrations': 'onRegistrationOut',
            'click .charts': 'onChartClick',
	    'tap .charts': 'onChartClick',
	    'mouseover .charts': 'onChartOver',
            'mouseout .charts': 'onChartOut',
            'click .maps': 'onMapClick',
	    'tap .maps': 'onMapClick',
	    'mouseover .maps': 'onMapOver',
            'mouseout .maps': 'onMapOut',
            'click .problems': 'onProblemClick',
	    'tap .problems': 'onProblemClick',
	    'mouseover .problems': 'onProblemOver',
            'mouseout .problems': 'onProblemOut',
            'click .experiments': 'onExperimentClick',
	    'tap .experiments': 'onExperimentClick',
            'mouseover .experiments': 'onExperimentOver',
            'mouseout .experiments': 'onExperimentOut',
            'click .solutions': 'onSolutionClick',
	    'tap .solutions': 'onSolutionClick',
	    'mouseover .solutions': 'onSolutionOver',
            'mouseout .solutions': 'onSolutionOut'
   	},

	initialize: function() 
        {
		//alert('yo');
                var xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function() 
                {
                       //alert(xhttp.readyState)
                       if (xhttp.readyState == 4 && xhttp.status == 200) 
                       {
                             //alert('Yo'+xhttp.responseText);
                             //alert( document.getElementById("commentArea").innerHTML);
                             IslandDataObj =  JSON.parse(xhttp.responseText);
                             //alert(IslandDataObj.ads.definition);
                             var changeText = document.getElementById("home_definition");
                             changeText.innerHTML  =IslandDataObj.home.definition;
                       }
                       //else {alert('loading');}
                };
  
                xhttp.open("GET", "http://louvrienfomasyon.ekendotech.com/Data/py/bkdtakbdb.py/louvriEnfomasyon/English/Home/", true);
                xhttp.send();
	},

        onQuestionClick: function(evt) 
        {
		//alert('yo');
                var xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function() 
                {
                       //alert(xhttp.readyState)
                       if (xhttp.readyState == 4 && xhttp.status == 200) 
                       {
                             //alert('Yo'+xhttp.responseText);
                             //alert( document.getElementById("commentArea").innerHTML);
                             IslandDataObj =  JSON.parse(xhttp.responseText);
                             //alert(IslandDataObj.ads.definition);
                             var changeText = document.getElementById("home_definition");
                             changeText.innerHTML  =IslandDataObj.questions.definition;
                             var changeBlogText = document.getElementById("home_blog");
                             changeBlogText.innerHTML = '...';
                             var changeTagText = document.getElementById("home_tags");
                             changeTagText.innerHTML = IslandDataObj.questions.tag1;
                             changeTagText.innerHTML += '&nbsp;&nbsp;'
                             changeTagText.innerHTML += IslandDataObj.questions.tag2;
                             changeTagText.innerHTML += '&nbsp;&nbsp;'
                             changeTagText.innerHTML += IslandDataObj.questions.tag3;
                             changeTagText.innerHTML += '&nbsp;&nbsp;'
                             changeTagText.innerHTML += IslandDataObj.questions.tag4;
                             changeTagText.innerHTML += '&nbsp;&nbsp;'
                             changeTagText.innerHTML += IslandDataObj.questions.tag5;
                             changeTagText.innerHTML += '&nbsp;&nbsp;'
                             changeTagText.innerHTML += IslandDataObj.questions.tag6;
                       }
                };
  
                xhttp.open("GET", "http://louvrienfomasyon.ekendotech.com/Data/py/bkdtakbdb.py/louvriEnfomasyon/English/Questions/", true);
                xhttp.send();
	},

        onQuestionOver: function(evt)
        {
		// change the look of tings
                document.getElementById("Questions").style.color = 'blue';
	},
        
        onQuestionOut: function(evt)
        {
		// change the look of tings
                document.getElementById("Questions").style.color = 'black';
	},

        onAnswerClick: function(evt) 
        {
		//alert('yo');
                var xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function() 
                {
                       //alert(xhttp.readyState)
                       if (xhttp.readyState == 4 && xhttp.status == 200) 
                       {
                             //alert('Yo'+xhttp.responseText);
                             //alert( document.getElementById("commentArea").innerHTML);
                             IslandDataObj =  JSON.parse(xhttp.responseText);
                             //alert(IslandDataObj.ads.definition);
                             var changeText = document.getElementById("home_definition");
                             changeText.innerHTML  =IslandDataObj.answers.definition;
                             var changeBlogText = document.getElementById("home_blog");
                             changeBlogText.innerHTML = '...';
                             var changeTagText = document.getElementById("home_tags");
                             changeTagText.innerHTML = IslandDataObj.answers.tag1;
                             changeTagText.innerHTML += '&nbsp;&nbsp;'
                             changeTagText.innerHTML += IslandDataObj.answers.tag2;
                             changeTagText.innerHTML += '&nbsp;&nbsp;'
                             changeTagText.innerHTML += IslandDataObj.answers.tag3;
                             changeTagText.innerHTML += '&nbsp;&nbsp;'
                             changeTagText.innerHTML += IslandDataObj.answers.tag4;
                             changeTagText.innerHTML += '&nbsp;&nbsp;'
                             changeTagText.innerHTML += IslandDataObj.answers.tag5;
                             changeTagText.innerHTML += '&nbsp;&nbsp;'
                             changeTagText.innerHTML += IslandDataObj.answers.tag6;
                       }
                       //else {alert('loading');}
                };
  
                xhttp.open("GET", "http://louvrienfomasyon.ekendotech.com/Data/py/bkdtakbdb.py/louvriEnfomasyon/English/Answers/", true);
                xhttp.send();
	},

        onAnswerOver: function(evt)
        {
		// change the look of tings
                document.getElementById("Answers").style.color = 'blue';
	},
        
        onAnswerOut: function(evt)
        {
		// change the look of tings
                document.getElementById("Answers").style.color = 'black';
	},

        
  	onAdClick: function(evt) 
        {
		//alert('yo');
                var xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function() 
                {
                       //alert(xhttp.readyState)
                       if (xhttp.readyState == 4 && xhttp.status == 200) 
                       {
                             //alert('Yo'+xhttp.responseText);
                             //alert( document.getElementById("commentArea").innerHTML);
                             IslandDataObj =  JSON.parse(xhttp.responseText);
                             //alert(IslandDataObj.ads.definition);
                             var changeText = document.getElementById("home_definition");
                             changeText.innerHTML  =IslandDataObj.ads.definition;
                             var changeBlogText = document.getElementById("home_blog");
                             changeBlogText.innerHTML = '...';
                             var changeTagText = document.getElementById("home_tags");
                             changeTagText.innerHTML = IslandDataObj.ads.tag1;
                             changeTagText.innerHTML += '&nbsp;&nbsp;'
                             changeTagText.innerHTML += IslandDataObj.ads.tag2;
                             changeTagText.innerHTML += '&nbsp;&nbsp;'
                             changeTagText.innerHTML += IslandDataObj.ads.tag3;
                             changeTagText.innerHTML += '&nbsp;&nbsp;'
                             changeTagText.innerHTML += IslandDataObj.ads.tag4;
                             changeTagText.innerHTML += '&nbsp;&nbsp;'
                             changeTagText.innerHTML += IslandDataObj.ads.tag5;
                             changeTagText.innerHTML += '&nbsp;&nbsp;'
                             changeTagText.innerHTML += IslandDataObj.ads.tag6;
                             
                       }
                };
  
                xhttp.open("GET", "http://louvrienfomasyon.ekendotech.com/Data/py/bkdtakbdb.py/louvriEnfomasyon/English/Ads/", true);
                xhttp.send();
	},

        onChange: function(evt) 
        {
    	      alert('sumpytin changed');
    	      //upate View
        },

        onAdOver: function(evt)
        {
		// change the look of tings
                document.getElementById("Ads").style.color = 'blue';
	},
        
        onAdOut: function(evt)
        {
		// change the look of tings
                document.getElementById("Ads").style.color = 'black';
	},

        onProductClick: function(evt) 
        {
		//alert('yo');
                var xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function() 
                {
                       //alert(xhttp.readyState)
                       if (xhttp.readyState == 4 && xhttp.status == 200) 
                       {
                             //alert('Yo'+xhttp.responseText);
                             //alert( document.getElementById("commentArea").innerHTML);
                             IslandDataObj =  JSON.parse(xhttp.responseText);
                             //alert(IslandDataObj.ads.definition);
                             var changeText = document.getElementById("home_definition");
                             changeText.innerHTML  =IslandDataObj.products.definition;
                             var changeBlogText = document.getElementById("home_blog");
                             changeBlogText.innerHTML = '...';
                             var changeTagText = document.getElementById("home_tags");
                             changeTagText.innerHTML = IslandDataObj.products.tag1;
                             changeTagText.innerHTML += '&nbsp;&nbsp;'
                             changeTagText.innerHTML += IslandDataObj.products.tag2;
                             changeTagText.innerHTML += '&nbsp;&nbsp;'
                             changeTagText.innerHTML += IslandDataObj.products.tag3;
                             changeTagText.innerHTML += '&nbsp;&nbsp;'
                             changeTagText.innerHTML += IslandDataObj.products.tag4;
                             changeTagText.innerHTML += '&nbsp;&nbsp;'
                             changeTagText.innerHTML += IslandDataObj.products.tag5;
                             changeTagText.innerHTML += '&nbsp;&nbsp;'
                             changeTagText.innerHTML += IslandDataObj.products.tag6;
                       }
                };
  
                xhttp.open("GET", "http://louvrienfomasyon.ekendotech.com/Data/py/bkdtakbdb.py/louvriEnfomasyon/English/Products/", true);
                xhttp.send();
	},

        onProductOver: function(evt)
        {
		// change the look of tings
                document.getElementById("Products").style.color = 'blue';
	},
        
        onProductOut: function(evt)
        {
		// change the look of tings
                document.getElementById("Products").style.color = 'black';
	},
  
        onProfileClick: function(evt) 
        {
		//alert('yo');
                var xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function() 
                {
                       //alert(xhttp.readyState)
                       if (xhttp.readyState == 4 && xhttp.status == 200) 
                       {
                             //alert('Yo'+xhttp.responseText);
                             //alert( document.getElementById("commentArea").innerHTML);
                             IslandDataObj =  JSON.parse(xhttp.responseText);
                             //alert(IslandDataObj.ads.definition);
                             var changeText = document.getElementById("home_definition");
                             changeText.innerHTML  =IslandDataObj.profiles.definition;
                             var changeBlogText = document.getElementById("home_blog");
                             changeBlogText.innerHTML = '...';
                             var changeTagText = document.getElementById("home_tags");
                             changeTagText.innerHTML = IslandDataObj.profiles.tag1;
                             changeTagText.innerHTML += '&nbsp;&nbsp;'
                             changeTagText.innerHTML += IslandDataObj.profiles.tag2;
                             changeTagText.innerHTML += '&nbsp;&nbsp;'
                             changeTagText.innerHTML += IslandDataObj.profiles.tag3;
                             changeTagText.innerHTML += '&nbsp;&nbsp;'
                             changeTagText.innerHTML += IslandDataObj.profiles.tag4;
                             changeTagText.innerHTML += '&nbsp;&nbsp;'
                             changeTagText.innerHTML += IslandDataObj.profiles.tag5;
                             changeTagText.innerHTML += '&nbsp;&nbsp;'
                             changeTagText.innerHTML += IslandDataObj.profiles.tag6;
                       }
                };
  
                xhttp.open("GET", "http://louvrienfomasyon.ekendotech.com/Data/py/bkdtakbdb.py/louvriEnfomasyon/English/Profiles/", true);
                xhttp.send();
	},

        onProfileOver: function(evt)
        {
		// change the look of tings
                document.getElementById("Profiles").style.color = 'blue';
	},
        
        onProfileOut: function(evt)
        {
		// change the look of tings
                document.getElementById("Profiles").style.color = 'black';
	},

        onDefinitionClick: function(evt) 
        {
		//alert('yo');
                var xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function() 
                {
                       //alert(xhttp.readyState)
                       if (xhttp.readyState == 4 && xhttp.status == 200) 
                       {
                             //alert('Yo'+xhttp.responseText);
                             //alert( document.getElementById("commentArea").innerHTML);
                             IslandDataObj =  JSON.parse(xhttp.responseText);
                             //alert(IslandDataObj.ads.definition);
                             var changeText = document.getElementById("home_definition");
                             changeText.innerHTML  =IslandDataObj.definitions.definition;
                             var changeBlogText = document.getElementById("home_blog");
                             changeBlogText.innerHTML = '...';
                            var changeTagText = document.getElementById("home_tags");
                             changeTagText.innerHTML = IslandDataObj.definitions.tag1;
                             changeTagText.innerHTML += '&nbsp;&nbsp;'
                             changeTagText.innerHTML += IslandDataObj.definitions.tag2;
                             changeTagText.innerHTML += '&nbsp;&nbsp;'
                             changeTagText.innerHTML += IslandDataObj.definitions.tag3;
                             changeTagText.innerHTML += '&nbsp;&nbsp;'
                             changeTagText.innerHTML += IslandDataObj.definitions.tag4;
                             changeTagText.innerHTML += '&nbsp;&nbsp;'
                             changeTagText.innerHTML += IslandDataObj.definitions.tag5;
                             changeTagText.innerHTML += '&nbsp;&nbsp;'
                             changeTagText.innerHTML += IslandDataObj.definitions.tag6;
                       }
                       //else {alert('loading');}
                };
  
                xhttp.open("GET", "http://louvrienfomasyon.ekendotech.com/Data/py/bkdtakbdb.py/louvriEnfomasyon/English/Definitions/", true);
                xhttp.send();
	},

        onDefinitionOver: function(evt)
        {
		// change the look of tings
                document.getElementById("Definitions").style.color = 'blue';
	},
        
        onDefinitionOut: function(evt)
        {
		// change the look of tings
                document.getElementById("Definitions").style.color = 'black';
	},

        onRegistrationClick: function(evt) 
        {
		//alert('yo');
                var xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function() 
                {
                       //alert(xhttp.readyState)
                       if (xhttp.readyState == 4 && xhttp.status == 200) 
                       {
                             //alert('Yo'+xhttp.responseText);
                             //alert( document.getElementById("commentArea").innerHTML);
                             IslandDataObj =  JSON.parse(xhttp.responseText);
                             //alert(IslandDataObj.ads.definition);
                             var changeText = document.getElementById("home_definition");
                             changeText.innerHTML  =IslandDataObj.registrations.definition;
                             var changeBlogText = document.getElementById("home_blog");
                             changeBlogText.innerHTML = '...';
                             var changeTagText = document.getElementById("home_tags");
                             changeTagText.innerHTML = IslandDataObj.registrations.tag1;
                             changeTagText.innerHTML += '&nbsp;&nbsp;'
                             changeTagText.innerHTML += IslandDataObj.registrations.tag2;
                             changeTagText.innerHTML += '&nbsp;&nbsp;'
                             changeTagText.innerHTML += IslandDataObj.registrations.tag3;
                             changeTagText.innerHTML += '&nbsp;&nbsp;'
                             changeTagText.innerHTML += IslandDataObj.registrations.tag4;
                             changeTagText.innerHTML += '&nbsp;&nbsp;'
                             changeTagText.innerHTML += IslandDataObj.registrations.tag5;
                             changeTagText.innerHTML += '&nbsp;&nbsp;'
                             changeTagText.innerHTML += IslandDataObj.registrations.tag6;
                       }
                };
  
                xhttp.open("GET", "http://louvrienfomasyon.ekendotech.com/Data/py/bkdtakbdb.py/louvriEnfomasyon/English/Registrations/", true);
                xhttp.send();
	},

        onRegistrationOver: function(evt)
        {
		// change the look of tings
                document.getElementById("Registrations").style.color = 'blue';
	},
        
        onRegistrationOut: function(evt)
        {
		// change the look of tings
                document.getElementById("Registrations").style.color = 'black';
	},

        onChartClick: function(evt) 
        {
		//alert('yo');
                var xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function() 
                {
                       //alert(xhttp.readyState)
                       if (xhttp.readyState == 4 && xhttp.status == 200) 
                       {
                             //alert('Yo'+xhttp.responseText);
                             IslandDataObj =  JSON.parse(xhttp.responseText);
                             var changeText = document.getElementById("home_definition");
                             changeText.innerHTML  =IslandDataObj.charts.definition;
                             var changeBlogText = document.getElementById("home_blog");
                             changeBlogText.innerHTML = '...';
                             var changeTagText = document.getElementById("home_tags");
                             changeTagText.innerHTML = IslandDataObj.charts.tag1;
                             changeTagText.innerHTML += '&nbsp;&nbsp;'
                             changeTagText.innerHTML += IslandDataObj.charts.tag2;
                             changeTagText.innerHTML += '&nbsp;&nbsp;'
                             changeTagText.innerHTML += IslandDataObj.charts.tag3;
                             changeTagText.innerHTML += '&nbsp;&nbsp;'
                             changeTagText.innerHTML += IslandDataObj.charts.tag4;
                             changeTagText.innerHTML += '&nbsp;&nbsp;'
                             changeTagText.innerHTML += IslandDataObj.charts.tag5;
                             changeTagText.innerHTML += '&nbsp;&nbsp;'
                             changeTagText.innerHTML += IslandDataObj.charts.tag6;
                       }
                };
  
                xhttp.open("GET", "http://louvrienfomasyon.ekendotech.com/Data/py/bkdtakbdb.py/louvriEnfomasyon/English/Charts/", true);
                xhttp.send();
	},

        onChartOver: function(evt)
        {
		// change the look of tings
                document.getElementById("Charts").style.color = 'blue';
	},
        
        onChartOut: function(evt)
        {
		// change the look of tings
                document.getElementById("Charts").style.color = 'black';
	},

        onMapClick: function(evt) 
        {
		//alert('yo');
                var xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function() 
                {
                       //alert(xhttp.readyState)
                       if (xhttp.readyState == 4 && xhttp.status == 200) 
                       {
                             //alert('Yo'+xhttp.responseText);
                             //alert( document.getElementById("commentArea").innerHTML);
                             IslandDataObj =  JSON.parse(xhttp.responseText);
                             //alert(IslandDataObj.ads.definition);
                             var changeText = document.getElementById("home_definition");
                             changeText.innerHTML  =IslandDataObj.maps.definition;
                             var changeBlogText = document.getElementById("home_blog");
                             changeBlogText.innerHTML = '...';
                             var changeTagText = document.getElementById("home_tags");
                             changeTagText.innerHTML = IslandDataObj.maps.tag1;
                             changeTagText.innerHTML += '&nbsp;&nbsp;'
                             changeTagText.innerHTML += IslandDataObj.maps.tag2;
                             changeTagText.innerHTML += '&nbsp;&nbsp;'
                             changeTagText.innerHTML += IslandDataObj.maps.tag3;
                             changeTagText.innerHTML += '&nbsp;&nbsp;'
                             changeTagText.innerHTML += IslandDataObj.maps.tag4;
                             changeTagText.innerHTML += '&nbsp;&nbsp;'
                             changeTagText.innerHTML += IslandDataObj.maps.tag5;
                             changeTagText.innerHTML += '&nbsp;&nbsp;'
                             changeTagText.innerHTML += IslandDataObj.maps.tag6;
                       }
                };
  
                xhttp.open("GET", "http://louvrienfomasyon.ekendotech.com/Data/py/bkdtakbdb.py/louvriEnfomasyon/English/Maps/", true);
                xhttp.send();
	},

        onMapOver: function(evt)
        {
		// change the look of tings
                document.getElementById("Maps").style.color = 'blue';
	},
        
        onMapOut: function(evt)
        {
		// change the look of tings
                document.getElementById("Maps").style.color = 'black';
	},
  
        onProblemClick: function(evt) 
        {
		//alert('yo');
                var xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function() 
                {
                       //alert(xhttp.readyState)
                       if (xhttp.readyState == 4 && xhttp.status == 200) 
                       {
                             //alert('Yo'+xhttp.responseText);
                             //alert( document.getElementById("commentArea").innerHTML);
                             IslandDataObj =  JSON.parse(xhttp.responseText);
                             //alert(IslandDataObj.ads.definition);
                             var changeText = document.getElementById("home_definition");
                             changeText.innerHTML  =IslandDataObj.problems.definition;
                             var changeBlogText = document.getElementById("home_blog");
                             changeBlogText.innerHTML = '...';
                             var changeTagText = document.getElementById("home_tags");
                             changeTagText.innerHTML = IslandDataObj.problems.tag1;
                             changeTagText.innerHTML += '&nbsp;&nbsp;'
                             changeTagText.innerHTML += IslandDataObj.problems.tag2;
                             changeTagText.innerHTML += '&nbsp;&nbsp;'
                             changeTagText.innerHTML += IslandDataObj.problems.tag3;
                             changeTagText.innerHTML += '&nbsp;&nbsp;'
                             changeTagText.innerHTML += IslandDataObj.problems.tag4;
                             changeTagText.innerHTML += '&nbsp;&nbsp;'
                             changeTagText.innerHTML += IslandDataObj.problems.tag5;
                             changeTagText.innerHTML += '&nbsp;&nbsp;'
                             changeTagText.innerHTML += IslandDataObj.problems.tag6;
                       }
                       //else {alert('loading');}
                };
  
                xhttp.open("GET", "http://louvrienfomasyon.ekendotech.com/Data/py/bkdtakbdb.py/louvriEnfomasyon/English/Problems/", true);
                xhttp.send();
	},

        onProblemOver: function(evt)
        {
		// change the look of tings
                document.getElementById("Problems").style.color = 'blue';
	},
        
        onProblemOut: function(evt)
        {
		// change the look of tings
                document.getElementById("Problems").style.color = 'black';
	},

        onExperimentClick: function(evt) 
        {
		//alert('yo');
                var xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function() 
                {
                       //alert(xhttp.readyState)
                       if (xhttp.readyState == 4 && xhttp.status == 200) 
                       {
                             //alert('Yo'+xhttp.responseText);
                             //alert( document.getElementById("commentArea").innerHTML);
                             IslandDataObj =  JSON.parse(xhttp.responseText);
                             //alert(IslandDataObj.ads.definition);
                             var changeText = document.getElementById("home_definition");
                             changeText.innerHTML  =IslandDataObj.experiments.definition;
                             var changeBlogText = document.getElementById("home_blog");
                             changeBlogText.innerHTML = '...';
                             var changeTagText = document.getElementById("home_tags");
                             changeTagText.innerHTML = IslandDataObj.experiments.tag1;
                             changeTagText.innerHTML += '&nbsp;&nbsp;'
                             changeTagText.innerHTML += IslandDataObj.experiments.tag2;
                             changeTagText.innerHTML += '&nbsp;&nbsp;'
                             changeTagText.innerHTML += IslandDataObj.experiments.tag3;
                             changeTagText.innerHTML += '&nbsp;&nbsp;'
                             changeTagText.innerHTML += IslandDataObj.experiments.tag4;
                             changeTagText.innerHTML += '&nbsp;&nbsp;'
                             changeTagText.innerHTML += IslandDataObj.experiments.tag5;
                             changeTagText.innerHTML += '&nbsp;&nbsp;'
                             changeTagText.innerHTML += IslandDataObj.experiments.tag6;
                       }
                       //else {alert('loading');}
                };
  
                xhttp.open("GET", "http://louvrienfomasyon.ekendotech.com/Data/py/bkdtakbdb.py/louvriEnfomasyon/English/Experiments/", true);
                xhttp.send();
	},

        onExperimentOver: function(evt)
        {
		// change the look of tings
                document.getElementById("Experiments").style.color = 'blue';
	},
        
        onExperimentOut: function(evt)
        {
		// change the look of tings
                document.getElementById("Experiments").style.color = 'black';
	},

        onSolutionClick: function(evt) 
        {
		//alert('yo');
                var xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function() 
                {
                       //alert(xhttp.readyState)
                       if (xhttp.readyState == 4 && xhttp.status == 200) 
                       {
                             //alert('Yo'+xhttp.responseText);
                             //alert( document.getElementById("commentArea").innerHTML);
                             IslandDataObj =  JSON.parse(xhttp.responseText);
                             //alert(IslandDataObj.ads.definition);
                             var changeText = document.getElementById("home_definition");
                             changeText.innerHTML  =IslandDataObj.solutions.definition;
                             var changeBlogText = document.getElementById("home_blog");
                             changeBlogText.innerHTML = '...';
                             var changeTagText = document.getElementById("home_tags");
                             changeTagText.innerHTML = IslandDataObj.solutions.tag1;
                             changeTagText.innerHTML += '&nbsp;&nbsp;'
                             changeTagText.innerHTML += IslandDataObj.solutions.tag2;
                             changeTagText.innerHTML += '&nbsp;&nbsp;'
                             changeTagText.innerHTML += IslandDataObj.solutions.tag3;
                             changeTagText.innerHTML += '&nbsp;&nbsp;'
                             changeTagText.innerHTML += IslandDataObj.solutions.tag4;
                             changeTagText.innerHTML += '&nbsp;&nbsp;'
                             changeTagText.innerHTML += IslandDataObj.solutions.tag5;
                             changeTagText.innerHTML += '&nbsp;&nbsp;'
                             changeTagText.innerHTML += IslandDataObj.solutions.tag6;
                       }
                       //else {alert('loading');}
                };
  
                xhttp.open("GET", "http://louvrienfomasyon.ekendotech.com/Data/py/bkdtakbdb.py/louvriEnfomasyon/English/Solutions/", true);
                xhttp.send();
	},

        onSolutionOver: function(evt)
        {
		// change the look of tings
                document.getElementById("Solutions").style.color = 'blue';
	},
        
        onSolutionOut: function(evt)
        {
		// change the look of tings
                document.getElementById("Solutions").style.color = 'black';
	},


	render: function() 
        {
	    //alert('rendering:');
		//render Stuffs based on model
  	}

  });
 
  showad();

  var App = new IslandDataView;
});
