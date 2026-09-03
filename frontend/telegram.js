alert("1");
 var results = msg.payload.businesses;

 if (results.length > 0) {
 var responses = [];
 for (var i=0; i<results.length; i++) {
     var result = results[i];
     var categories = [];
     for (var j=0; j<result.categories.length; j++) {
         categories.push(result.categories[j][0]);
     }
     responses.push({
         type: "venue",
         id: result.id,
         title: result.name + " (" + categories.join(", ") + ")",
         thumb_url: result.image_url,
         latitude: result.location.coordinate.latitude,
         longitude: result.location.coordinate.longitude,
         address: result.review_count + " reviews. Rating: " + result.rating + ". \n" +
                 result.location.display_address.join(" ")
     });
 }
     msg.payload = responses;
     return msg;
 } else {
     msg.payload = [{
         type: "article",
         id: "no-result",
         title: "No Yelp Result Found",
         input_message_content: {
             message_text: 'e.g., "dinner near downtown Chicago"'
         },
         description: 'e.g., "dinner near downtown Chicago"'
     }];

     return msg;
 }