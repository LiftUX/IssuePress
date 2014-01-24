
## Official repo for the IssuePress plugin for Wordpress

The plugin creates a page template that loads an angular app that interacts with the github v3 api.


## Getting Started

You'll need node, npm & grunt-cli for development. Make sure you have those installed & up to date.. 

- `git checkout` the plugin into a local WP install inside `wp-content/plugins/`
- `cd` into the IP main directory.
- run `npm install`, this will install all the npm dev dependancies
- run `grunt`
- begin working!


#### Note On Grunt

Right now we are using it to concat & lint our JS. This enables us to keep the maintainability of many different feature-based modules while keeping the performance of loading a single plugin .js asset.

It takes all .js files in `/src` and build it into `/build/main.js`, which we register with WP and have it load. 

I've created a few commands to run specific grunt tasks, they are as follows:

- `grunt` - this is the default task, and is tied to the 'watch' command, it will watch files for changes and upon seeing those changes it will lint & build the project or if scss files change, will compile those down.
- `grunt lint` - running this will lint all of the .js that would typically be built. 
- `grunt build` - running this will build the .js in `/src` down into `/build/main.js`

For general use, just use default `grunt` command, when you're ready to set a git commit (assuming you aren't changing branches), just add the `build/main.js` file as it reflects what it should look like at that give point in the git history.


## Note On Composer deps

*You shouldn't ever need to edit anything in the `/vendor/` directory, use the [composter](http://getcomposer.org/) cli to update these packages should it be required. *


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


### Wordpress Admin

No github repo/issue data will be stored in wordpress db. We will however, need a user account to authenticate and select IssuePress active repos.

We'll need: (for testing)
- username
- password

Once we have that, we'll be able to send a request out and get a list of all their accessable repos, which, will allow us to let the WP admin select which will be Active to IP.

- active\_repos  : Array()

We'll be using OAUTH for actual release.


