## Explaination

### The problem to be solved.
- Create an app or plugin to crawl a homepage which means the probem is how to retrieve or get the links on the homepage, I selected to create the wordpress plugin since the company's products are wordpress plugins. 
- The plugin must be able to crawl through the homepage to find links on the homepage

### A technical specification of the design.

1. Setting up the menu which consist of Dashboard Page and Results Page.

2. Dashboard Page (Submenu)
	- This page have the start crawl button
	- When the button is clicked it will start the instantiation of of the pagecrawler class and calls the register method.
	- It will then call the crawl function to start the crawling process.
		- Deleting the previously crawled links from the database.
		- Get the new links from the homepage.
		- Generate a new sitemap.html
		- Generate a new homepage.html
	- Display the links from the Database.
	- Reset the schedule event (every hour).
3. Result Page (Submenu)
	- Display the links from the Database.

### The technical decisions made and why.

1. Initialization of the Menu.
   - On the plugin.php file, I have added 3 methods in order to make the pages manageable. 
		- static method get_service, this returns an array of classes, This would help for future improvement of the plugin, when there will be new functionalities need to be added. It will be easy to add new set of classes to the plugin
		- method register_services will loop through the classes found on the get_services method to be able to instantiate them by calling the static method instantiate.
		- static method instantiate is responsible for instantiating the array of classed found on the get_service method.

2. The only class being instantiated is the Admin.php (Admin:class). This is responsible for the menu and the submenu of the plugin, Which in turn instantiate the MenuSetting::class.

3. MenuSetting.php is responsible in creating the submenu of the plugin. Both Admin.php and MenuSetting.php handles the main menu and the submenu respectively. which allows for easier addition of future menus.

4. PageCrawler is the main class which handles the main functionality of the plugin. Each steps has been separated into its own functions, actions and filters. this way it is easier to manage the plugin functionality. wpmc_start_craw function is the main function which calls other actions and filters during the crawling process.

### How your solution achieves the admin’s desired outcome per the user story
- My solution has achieved the admins desire outcome through the accomplishment of the required results. These results are based on the user story provided with the guidance of the proposed solution as enumerated.
	-	Add a back-end admin page (for WP - settings page) where the admin can log in and then manually trigger a crawl and view the results.
		- Solution: 
			- Since its a wordpress plugin then the admin can login through the wp-admin page
			- Button is provide on the dashboard to manually trigger the crawl and display the resuts.
			- A submenu of results is also provided if the admin want to view the previously generated results, this results will be overwritten when the button has been clicked.

- The crawl task should do the following:
	- Delete the results from the last crawl (i.e. in temporary storage), if they exist.
		- Solution:
			- On every crawl database entry are deleted and replaced with the latest.
	- Delete the sitemap.html file if it exists as well as the homepage .html file.
		- Solution:
			- Every crawl sitemap.html is generated does deleting automatically or overwritten with the new links being returned during the homepage crawl.
- Extract all of the internal hyperlinks present in the homepage, i.e. results.
	- Solution:
		- crawling function has been implemented.
- Store results in the database.
	- Solution:
		- Every Crawl will save the links as a custom post type ('wwpmcrawler_links')
- Display the results on the admin page.
	- Solution;
		- Being Displayed every click of the crawl button
		- Submenu Results is also available to display previous results
- Save the homepage as a .html file on the server.
	- Solution:
		- retrieve the body of the homepage and save it as homepage.html inside Files folder of the plugin.
- Create a sitemap.html file that shows the results as a sitemap list structure.
	- Solution:
		- All of the links are saved as an unordered list on to a html file inside the Files folder of the plugin with the filename sitemap.html
- When the admin triggers a crawl:
	- Set a crawl to run immediately.
		- Solution:
			- wp_schedule_event is created on activation of the plugin.
	- Then set the crawl to run automatically every hour ⏰🤖.
		- Solution:
			- on every click of the button start crawl. the wp_schedule_event is cleared and re initialized this way the schedule will reset the schedule.			
	- When the admin requests to view the results, pull the results from storage and display them on the admin page.
		- Solution:
			- The submenu results will provide the results from the database.			
- If an error happens, display an error notice to inform of what happened and guide for what to do.
	- Solution:
		- Added a error display if there is no link available on the homepage., This prevents triggering an error exception display.
