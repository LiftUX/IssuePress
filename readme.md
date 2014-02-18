
## Official repo for the IssuePress plugin for Wordpress

The plugin creates a page template that loads an angular app that interacts with the github v3 api.


## Getting Started

### Basic Set up instructions

- In a standard WP install, git clone the master branch into the plugins directory.
- Enable the plugin and visit, https://github.com/settings/tokens/new to generate a new github access token (Make sure public, repo & user at least are selected.)
- Paste this token into the IP Settings page at [/wp-admin/admin.php?page=issuepress_options](/wp-admin/admin.php?page=issuepress_options)
- Select the page you want IP to display on
- Select the repositories you want IP to use
- Visit that page on the front-end
- Profit! $$$

### Basic Editing instructions

You'll need node, npm & grunt-cli for development. Make sure you have those installed & up to date.. 

- `cd` into the IP main directory.
- run `npm install`, this will install all the npm dev dependancies
- run `grunt`
- begin working!


#### Notes On Grunt

Right now we are using it to concat & lint our JS. This enables us to keep the maintainability of many different feature-based modules while keeping the performance of loading a single plugin .js asset.

It takes all .js files in `/src` and build it into `/build/main.js`, which we register with WP and have it load. 

I've created a few commands to run specific grunt tasks, they are as follows:

- `grunt` - this is the default task, and is tied to the 'watch' command, it will watch files for changes and upon seeing those changes it will lint & build the project or if scss files change, will compile those down.
- `grunt lint` - running this will lint all of the .js that would typically be built. 
- `grunt build` - running this will build the .js in `/src` down into `/build/main.js`

For general use, just use default `grunt` command, when you're ready to set a git commit (assuming you aren't changing branches), just add the `build/main.js` file as it reflects what it should look like at that give point in the git history.



### Request flow

```
/plugin-page/
```
Page template to list defined repositories.

```
/plugin-page/repo/
```
List Issues on that defined repository.

```
/plugin-page/repo/ID
```
List Defined Issue in defined repository.


```
/plugin-page/repo/new
```
Add a new issue to defined repo.


