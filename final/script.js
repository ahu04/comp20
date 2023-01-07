
// const KEY = "bd18dc08d7954c4cae19b14f4f47eda8"
// const KEY = "79488a502b0548a2a5c775147fe33aa4"
// const KEY = "cbd2598021174749b69a6e76bcb83ff4"
// const KEY = "478cf94642dc4b7da69f909e9be9480e"
const KEY = "c50c7500434245e88c156e2399258fe1" /* FOR SUBMISSION USE */

// console.log("Note: Turning API Calls Off Temporarily to Preserve Spoonacular Points!\nUsing `example_recipe.json` file instead")
// const KEY = null


/* fetch_random
 * Input: N/A
 * Purpose: Using a fetch request to attain a random recipe from the Spoonacular
 *          API
 * Output: N/A
 */
async function fetch_random(){
    // Performing Fetch
    url = `https://api.spoonacular.com/recipes/random?number=1&apiKey=${KEY}`
    // url = test_fetch
    result = await fetch_req(url)

    if (result === null) return;
    
    // Main Recipe Content
    content = result.recipes[0]
    // content = result

    // Attaining Title
    $('#recipe_title').text(content.title);

    // Attaining Summary
    // Segments the Summary String For More Digestable Output
    summary = segment(content.summary, '.', 4)
    $('#recipe_summary').html(summary);

    // Attaining Image
    image = "Sorry, No Image Available!"
    if (content.image != "undefined")
        image = `<img src="${content.image}" alt="${content.title}">`
    $('#recipe_image').html(image)
}

/* recipe_search
 * Input: N/A
 * Purpose: Using a fetch request to search for a recipe matching specific
 *          qualifications
 * Output: N/A
 */
async function recipe_search() {
    // Constructing & Fetching Search URL
    num_results = 5;
    search_terms = generate_search()
    search_url = url_search_constructor(search_terms, num_results)

    result = await fetch_req(search_url)
    if (result === null) return;

    // Parsing Search Results for Product IDs
    items = result['results']
    ids = []
    items.forEach(item => { ids.push(item['id']) });

    id_urls = []
    ids.forEach(id => { 
        id_urls.push(`https://api.spoonacular.com/recipes/${id}/information?apiKey=${KEY}`) 
    });


    // Filling Correct HTML Block With Info
    jQuery("#search_results").html("")
    success = true;

    // id_urls = [test_fetch, test_fetch, test_fetch, test_fetch]

    i = 0
    for (const id_url of id_urls) {
        success = await build_search_result(id_url, i++, id_urls.length) && success
        
        if (!success) {
            jQuery("#search_results").html("<p><em>Sorry, no results were found. Please Try Again.</em></p>")
            break
        }
    }

    jQuery(document).ready(function() {
        jQuery('input[type=checkbox]').change(function() {
            this.value = this.checked
        });
    });

}

/* build_search_result
 * Input: The url to fetch, the current numerical search result and the total
 *        number of search results
 * Purpose: Using a fetch request to search for a recipe matching specific
 *          qualifications and adds/formats to the results onto the page
 * Output: N/A
 */
async function build_search_result(url, id_num, total_results) {
    // Performing Fetch
    content = await fetch_req(url)
    if (content === null) return false;

    background = "background-color: var(--mist)"
    if (id_num %2 == 1) {
        background = "background-color: var(--barossa)"
    }
    
    overall_price = (content.pricePerServing * content.servings / 100).toFixed(2)

    instructions = ""
    content.analyzedInstructions[0].steps.forEach(part => {
        instructions += "<li>" + part["step"] + "</li>"
    });

    instructions = instructions.trim().replaceAll("'", '')
    if (instructions.length < 10) { return false; }

    ingredients = ""
    for (j = 0; j < content.extendedIngredients.length; j++) {
        ingredients += "<li>" + JSON.stringify(content.extendedIngredients[j].original).substring(1, JSON.stringify(content.extendedIngredients[i].original).length - 1) + "</li>"
    }

    stripped_summary = strip_html(segment(content.summary, ".", 4))
    // stripped_summary = strip_html(content.summary)

    message =
`<div class="search_result" id="result_${id_num}" style="align-items: center;${background}">
    <h4 id="title_${id_num}">${content.title}</h4>
    <p id="summary_${id_num}"><em>${stripped_summary}</em></p>
    <input type="checkbox" id="id_${id_num}" name="select_${id_num}" value="false">
    <label for="id_${id_num}">Select For Addition!</label>
    
    <div class="json">
        <input type='hidden' name='json_${id_num}_title' value='${content.title}'/>
        <input type='hidden' name='json_${id_num}_summary' value='${content.summary}'/>
        <input type='hidden' name='json_${id_num}_instructions' value='${instructions}'/>
        <input type='hidden' name='json_${id_num}_ingredients' value='${ingredients}'/>
        <input type='hidden' name='json_${id_num}_price' value='${overall_price}'/>
        <input type='hidden' name='num_results' value='${total_results}'/>
    </div>
</div>
`

    jQuery("#search_results").append(message)
    return true;
}



/* url_search_constructor
 * Input: A dictionary of key-value pairs that describe what to search by as
 *        well as an integer indicating the number of results to return
 * Purpose: Turns the dictionary of search into a url than can be fetch
 *          requested
 * Output: The url to perform a complex search request
 */
function url_search_constructor(search_term, num_results) {
    url = "https://api.spoonacular.com/recipes/complexSearch?instructionsRequired=true&";

    categorized_queries = categorize_query(search_term)

    for (const [category, query] of Object.entries(categorized_queries)) {
        url += query + '&'
    }

    url += `number=${num_results}&apiKey=${KEY}`
    
    return url;
}

/* generate_search
 * Input: N/A
 * Purpose: Generates a dictionary that corresponds to a user-inputted recipe
 *          category search
 * Output: The dictionary of search terms and values
 */
function generate_search() {
    options = [...document.getElementsByClassName("drop-display")][0],
              descendants = options.getElementsByTagName('*');
    
    search_terms = []
    for (item of descendants) {
        class_name = item.className.trim()

        if (class_name == "item" || class_name == "item add") {
            text = item.textContent
            text = text.slice(0, text.length - 1).trim()

            search_terms.push(text)
        }
    }

    return search_terms;
}


/* fetch_req
 * Inputs:  The url corresponding to the data to be fetched
 * Purpose: To perform a fetch request on a given url
 * Return:  If successful, return the parsed data found on request 
 *          Else, prints an error to the console log and returns null
 */
async function fetch_req(url) {
    const response = await fetch(url);

    if (!response.ok) {
        console.log(`An error has occured: ${response.status}`);
        return null;
    }

    const response_json = await response.json();
    return response_json;
}



function categorize_query(options) {
    query_types = {"type": ["main course", "side dish", "dessert", "appetizer", "salad", "bread", "breakfast", "soup", "beverage", "sauce", "marinade", "fingerfood", "snack", "drink"],
                   "cuisine": ["African", "American", "British", "Cajun", "Caribbean", "Chinese", "Eastern European", "European", "French", "German", "Greek", "Indian", "Irish", "Italian", "Japanese", "Jewish", "Korean", "Latin American", "Mediterranean", "Mexican", "Middle Eastern", "Nordic", "Southern", "Spanish", "Thai", "Vietnamese"],
                   "diet": ["Gluten Free", "Ketogenic", "Vegetarian", "Lacto-Vegetarian", "Ovo-Vegetarian", "Vegan", "Pescetarian", "Paleo"]
                  };
    
    modified_query_types = {"type": [], "cuisine": [], "diet": [] };
    query_match = {"type": [], "cuisine": [], "diet": [] };


    // Making All Lists Lowercase
    for (const [key, list] of Object.entries(query_types)) {
        for (item of list) {
            modified_query_types[key].push(item.toLowerCase());
        }
    }

    query_types = modified_query_types

    // Checking For Matches
    for (option of options) {
        for (const [key, list] of Object.entries(query_types)) {
            if (list.includes(option.toLowerCase())) {
                query_match[key].push(camelize(option));
                continue;
            }
        }
    }

    // Constructing Query String Parts
    query_strings = {"type": "type=", "cuisine": "cuisine=", "diet": "diet=" };
    query_strings["type"] += query_match["type"].join('|')
    query_strings["cuisine"] += query_match["cuisine"].join('|')
    query_strings["diet"] += query_match["diet"].join(',')
     
    return query_strings
}


/* # * # * # * # *    String Helper Functions!    * # * # * # * # */
get_position = (str, m, i) => str.split(m, i).join(m).length;

segment = (str, m, i) => str.slice(0, get_position(str, m, i) + 1)

function camelize(str) {
  return str.replace(/(?:^\w|[A-Z]|\b\w)/g, function(word, index) {
    return index === 0 ? word.toLowerCase() : word.toUpperCase();
  }).replace(/\s+/g, '');
}

function strip_html(html)
{
   return html.replace(/<[^>]*>?/gm, '');
}