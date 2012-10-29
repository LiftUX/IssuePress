
## Official repo for the IssuePress plugin for Wordpress

The plugin creates a page template that loads a backbone app that interacts with the github v3 api.


### Request flow

```
/plugin-page/
```
List defined repositories.

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

We'll need:
- username
- password

Once we have that, we'll be able to send a request out and get a list of all their accessable repos, which, will allow us to let the WP admin select which will be Active to IP.

- active\_repos  : Array()




