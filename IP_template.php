<?php
/*
Template Name: IssuePress
*/
?>
<!doctype html>
<html data-ng-app="issuepress">
<head>
  <title>IssuePress</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">

<?php do_action('ip_head'); ?>

  <!--[if lt IE 9]><script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
</head>
<body>



<script>
var IP_repos = <?php echo UP_IssuePress::get_IP_repo_json(); ?>

var IP_root = "<?php echo UP_IssuePress::get_IP_root(); ?>"

</script>

<div class="content">
  <ng-include src="'<?php echo plugins_url('src/app/header.tpl.html', __FILE__); ?>'">
  </ng-include>

  <div class="left-column">

    <section class="breadcrumb">
      <a href="">Support</a>
      <a href="">Garage Band</a>
      <a href="">Music Player won't work</a>
    </section>

    <section class="search">
      <form>
        <input class="textbox" type="text" placeholder="Search"/>
        <div class="live-search-results">
          <div class="live-search-message">
            <div class="message-title">
              No Results
            </div>
            <div class="message-content">
              Aww shucks, nothing was found. Try updating your search term.
            </div>
          </div>
          <div class="live-search-results-header">
            Support Sections
          </div>
          <div class="issue-list-item">
            <a href="" class="issue-link">Open Section</a>
            <div class="issue-title">
              Issue with the Music Player for the homepage on Garage Band
            </div>
            <div class="issue-date">
              Last Updated: December 27, 2012
            </div>
          </div>
          <div class="issue-list-item">
            <a href="" class="issue-link">Open Section</a>
            <div class="issue-title">
              Issue with the Music Player for the homepage on Garage Band
            </div>
            <div class="issue-date">
              Last Updated: December 27, 2012
            </div>
          </div>
          <div class="live-search-results-header">
            Issues
          </div>
          <div class="issue-list-item">
            <a href="" class="issue-link">Open Issue</a>
            <div class="issue-title">
              Issue with the Music Player for the homepage on Garage Band
            </div>
            <div class="issue-date">
              Last Updated: December 27, 2012
            </div>
          </div>
        </div>
      </form>
    </section>

    <section class="message">
      <div class="message-title">
        Welcome to UpThemes Support
      </div>
      <div class="message-content">
        Donec id elit non mi porta gravida at eget metus. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Vestibulum id ligula porta felis euismod semper.
      </div>
    </section>

    <section class="recent-activity">
      <div class="section-title">
        Recent Activity
      </div>
      <div class="recent-activity-list">
        <div class="recent-activity-item">
          <div class="recent-activity-content">
            <div class="recent-activity-icon">
              <img src="https://secure.gravatar.com/avatar/4e7025ec1de9ec52b819278def51a13a?s=420&d=https://a248.e.akamai.net/assets.github.com%2Fimages%2Fgravatars%2Fgravatar-user-420.png"/>
            </div>
            <div class="recent-activity-message">
              <div class="recent-activity-title">
                Version 2.1.3 of Garage Band released today to the world.
              </div>
              <a href="">See release notes</a>
            </div>
          </div>
          <a href="" class="recent-activity-timeago">4 hours ago</a>
        </div>
      </div>
      <div class="recent-activity-item">
        <div class="recent-activity-content">
          <div class="recent-activity-message">
            <a href="" class="recent-activity-title">
              Issue with the Music Player for the homepage
            </a>
            <div class="recent-activity-meta">
              <a href="">imbradmiller</a> commented on an issue in <a href="">garageband</a>
            </div>
            <p>Maecenas sed diam eget risus varius blandit sit amet non magna. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Duis mollis, est non commodo luctus, nisi erat porttitor ligula, eget lacinia odio sem nec elit. Integer posuere erat a ante venenatis dapibus posuere velit aliquet. Aenean eu leo quam. Pellentesque ornare sem lacinia quam venenatis vestibulum.</p>
          </div>
        </div>
        <a href="" class="recent-activity-timeago">4 hours ago</a>
      </div>
      <div class="recent-activity-item">
        <div class="recent-activity-content">
          <div class="recent-activity-message">
            <div class="recent-activity-meta">
              <a href="">imbradmiller</a> opened an issue in <a href="">garageband</a>
            </div>
            <a href="" class="recent-activity-title">
              Issue with the Music Player for the homepage
            </a>
          </div>
        </div>
        <a href="" class="recent-activity-timeago">4 hours ago</a>
      </div>
      <div class="recent-activity-item">
        <div class="recent-activity-content">
          <div class="recent-activity-message">
            <div class="recent-activity-meta">
              <a href="">imbradmiller</a> closed an issue in <a href="">garageband</a>
            </div>
            <a href="" class="recent-activity-title">
              Issue with the Music Player for the homepage
            </a>
          </div>
        </div>
        <a href="" class="recent-activity-timeago">4 hours ago</a>
      </div>
    </section>

    <section class="create-issue">
      <div class="section-title">
        Create New Issue
      </div>
      <form data-validate="parsley">
        <div class="input-wrap">
          <input class="textbox required" type="text" placeholder="Title"/>
        </div>
        <div class="input-wrap">
          <textarea class="required" placeholder="Leave a comment"></textarea>
        </div>
        <input class="submit" type="submit" value="Create Issue"/>
      </form>
    </section>

    <section class="recent-activity">
      <div class="section-title">
        Recent Activity on Tickets you are Following
      </div>
      <div class="recent-activity-list">
        <div class="recent-activity-item">
          <div class="recent-activity-content">
            <a href="">imbradmiller</a> commented on issue <a href="">#421</a>
          </div>
          <a href="" class="recent-activity-timeago">4 hours ago</a>
        </div>
        <div class="recent-activity-item">
          <div class="recent-activity-content">
            <a href="">imbradmiller</a> commented on issue <a href="">#421</a>
          </div>
          <a href="" class="recent-activity-timeago">4 hours ago</a>
        </div>
        <div class="recent-activity-item">
          <div class="recent-activity-content">
            <a href="">imbradmiller</a> commented on issue <a href="">#421</a>
          </div>
          <a href="" class="recent-activity-timeago">4 hours ago</a>
        </div>
        <div class="recent-activity-item">
          <div class="recent-activity-content">
            <a href="">imbradmiller</a> commented on issue <a href="">#421</a>
          </div>
          <a href="" class="recent-activity-timeago">4 hours ago</a>
        </div>
        <div class="recent-activity-item">
          <div class="recent-activity-content">
            <a href="">imbradmiller</a> commented on issue <a href="">#421</a>
          </div>
          <a href="" class="recent-activity-timeago">4 hours ago</a>
        </div>
      </div>
    </section>

    <section class="issue-thread">
      <div class="comment">
        <div class="comment-meta">
          <a href="" class="author">
            <img src="https://secure.gravatar.com/avatar/4e7025ec1de9ec52b819278def51a13a?s=420&d=https://a248.e.akamai.net/assets.github.com%2Fimages%2Fgravatars%2Fgravatar-user-420.png"/>
            <span class="author-name">garand</span>
          </a>
          <span class="author-action">created an issue</span>
          <a href="" class="comment-timeago">1 month ago</a>
        </div>
        <div class="comment-content">
          <div class="comment-title">Music Player won't work</div>
          <p>Vestibulum id ligula porta felis euismod semper. Maecenas sed diam eget risus varius blandit sit amet non magna. Integer posuere erat a ante venenatis dapibus posuere velit aliquet. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor.</p>
          <div class="comment-tags">
            <a href="">Music</a>
            <a href="">Player</a>
            <a href="">Pissed</a>
          </div>
          <a href="" class="follow-thread">Follow Thread</a>
        </div>
      </div>
      <div class="comment">
        <div class="comment-meta">
          <a href="" class="author">
            <img src="https://secure.gravatar.com/avatar/4e7025ec1de9ec52b819278def51a13a?s=420&d=https://a248.e.akamai.net/assets.github.com%2Fimages%2Fgravatars%2Fgravatar-user-420.png"/>
            <span class="author-name">garand</span>
            <span class="author-action">said</span>
          </a>
          <a href="" class="comment-timeago">1 month ago</a>
        </div>
        <div class="comment-content">
          <p>Vestibulum id ligula porta felis euismod semper. Maecenas sed diam eget risus varius blandit sit amet non magna. Integer posuere erat a ante venenatis dapibus posuere velit aliquet. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor.</p>
        </div>
      </div>
      <div class="comment staff">
        <div class="comment-meta">
          <a href="" class="author">
            <img src="https://secure.gravatar.com/avatar/4e7025ec1de9ec52b819278def51a13a?s=420&d=https://a248.e.akamai.net/assets.github.com%2Fimages%2Fgravatars%2Fgravatar-user-420.png"/>
            <span class="author-name">garand</span>
            <span class="author-action">said</span>
          </a>
          <a href="" class="comment-timeago">1 month ago</a>
        </div>
        <div class="comment-content">
          <p>Vestibulum id ligula porta felis euismod semper. Maecenas sed diam eget risus varius blandit sit amet non magna. Integer posuere erat a ante venenatis dapibus posuere velit aliquet. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor.</p>
        </div>
      </div>
      <div class="comment">
        <div class="comment-meta">
          <a href="" class="author">
            <img src="https://secure.gravatar.com/avatar/4e7025ec1de9ec52b819278def51a13a?s=420&d=https://a248.e.akamai.net/assets.github.com%2Fimages%2Fgravatars%2Fgravatar-user-420.png"/>
            <span class="author-name">garand</span>
            <span class="author-action">said</span>
          </a>
          <a href="" class="comment-timeago">1 month ago</a>
        </div>
        <div class="comment-content">
          <p>Vestibulum id ligula porta felis euismod semper. Maecenas sed diam eget risus varius blandit sit amet non magna. Integer posuere erat a ante venenatis dapibus posuere velit aliquet. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor.</p>
        </div>
      </div>
      <form class="comment new-comment">
        <div class="comment-meta">
          <a href="" class="author">
            <img src="https://secure.gravatar.com/avatar/4e7025ec1de9ec52b819278def51a13a?s=420&d=https://a248.e.akamai.net/assets.github.com%2Fimages%2Fgravatars%2Fgravatar-user-420.png"/>
            <span class="author-action">Logged in as</span>
            <span class="author-name">garand</span>
          </a>
          <a href="" class="comment-timeago">Logout</a>
        </div>
        <div class="textarea-wrap">
          <textarea placeholder="Leave a comment"></textarea>
        </div>
        <input type="submit" class="submit" value="Post Comment"/>
      </form>
    </section>

    <section class="issue-list">
      <div class="section-title">
        Recently Updated Tickets
      </div>
      <div class="issue-list-item">
        <div class="issue-title">
          Issue with the Music Player for the homepage on Garage Band
        </div>
        <div class="issue-date">
          Last Updated: December 27, 2012
        </div>
        <div class="issue-description">
          Maecenas sed diam eget risus varius blandit sit amet non magna. Cras mattis consectetur purus sit amet fermentum. Aenean eu leo quam. Pellentesque ornare sem lacinia quam venenatis vestibulum. Integer posuere erat a ante venenatis dapibus posuere velit aliquet. Etiam porta sem malesuada magna mollis euismod. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor.
        </div>
        <a href="" class="issue-link">Open Issue</a>
      </div>
      <div class="issue-list-item">
        <div class="issue-title">
          Issue with the Music Player for the homepage on Garage Band
        </div>
        <div class="issue-date">
          Last Updated: December 27, 2012
        </div>
        <div class="issue-description">
          Maecenas sed diam eget risus varius blandit sit amet non magna. Cras mattis consectetur purus sit amet fermentum. Aenean eu leo quam. Pellentesque ornare sem lacinia quam venenatis vestibulum. Integer posuere erat a ante venenatis dapibus posuere velit aliquet. Etiam porta sem malesuada magna mollis euismod. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor.
        </div>
        <a href="" class="issue-link">Open Issue</a>
      </div>
      <div class="issue-list-item">
        <div class="issue-title">
          Issue with the Music Player for the homepage on Garage Band
        </div>
        <div class="issue-date">
          Last Updated: December 27, 2012
        </div>
        <div class="issue-description">
          Maecenas sed diam eget risus varius blandit sit amet non magna. Cras mattis consectetur purus sit amet fermentum. Aenean eu leo quam. Pellentesque ornare sem lacinia quam venenatis vestibulum. Integer posuere erat a ante venenatis dapibus posuere velit aliquet. Etiam porta sem malesuada magna mollis euismod. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor.
        </div>
        <a href="" class="issue-link">Open Issue</a>
      </div>
      <div class="issue-list-item">
        <div class="issue-title">
          Issue with the Music Player for the homepage on Garage Band
        </div>
        <div class="issue-date">
          Last Updated: December 27, 2012
        </div>
        <div class="issue-description">
          Maecenas sed diam eget risus varius blandit sit amet non magna. Cras mattis consectetur purus sit amet fermentum. Aenean eu leo quam. Pellentesque ornare sem lacinia quam venenatis vestibulum. Integer posuere erat a ante venenatis dapibus posuere velit aliquet. Etiam porta sem malesuada magna mollis euismod. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor.
        </div>
        <a href="" class="issue-link">Open Issue</a>
      </div>
      <div class="issue-list-item">
        <div class="issue-title">
          Issue with the Music Player for the homepage on Garage Band
        </div>
        <div class="issue-date">
          Last Updated: December 27, 2012
        </div>
        <div class="issue-description">
          Maecenas sed diam eget risus varius blandit sit amet non magna. Cras mattis consectetur purus sit amet fermentum. Aenean eu leo quam. Pellentesque ornare sem lacinia quam venenatis vestibulum. Integer posuere erat a ante venenatis dapibus posuere velit aliquet. Etiam porta sem malesuada magna mollis euismod. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor.
        </div>
        <a href="" class="issue-link">Open Issue</a>
      </div>
    </section>

  </div>

  <div class="right-column">

    <section class="support-sections">
      <div class="section-title">
        All Support Sections
      </div>
      <div class="section-nav">
        <a href="">Most Recent</a>
        <a href="">All</a>
      </div>
      <a href="" class="support-section">
        <div class="support-section-following">Follow</div>
        <div class="support-section-title">GarageBand Theme</div>
        <div class="support-section-date">December 27th, 2012</div>
      </a>
      <a href="" class="support-section">
        <div class="support-section-following">Follow</div>
        <div class="support-section-title">GarageBand Theme</div>
        <div class="support-section-date">December 27th, 2012</div>
      </a>
      <a href="" class="support-section">
        <div class="support-section-following">Following</div>
        <div class="support-section-title">GarageBand Theme</div>
        <div class="support-section-date">December 27th, 2012</div>
      </a>
      <a href="" class="support-section">
        <div class="support-section-following">Following</div>
        <div class="support-section-title">GarageBand Theme</div>
        <div class="support-section-date">December 27th, 2012</div>
      </a>
    </section>

    <section class="tickets-following">
      <div class="section-title">
        Tickets I'm Following
      </div>
      <a href="" class="ticket">
        <span class="ticket-title">Issue with the Music Player for the homepage on Garage Band</span>
        <span class="ticket-meta">imbradmiller said an hour ago</span>
        <span class="ticket-comment">This is exactly the issue I'm having and I fixed it by</span>
      </a>
      <a href="" class="ticket">
        <span class="ticket-title">Issue with the Music Player for the homepage on Garage Band</span>
        <span class="ticket-meta">imbradmiller said an hour ago</span>
        <span class="ticket-comment">This is exactly the issue I'm having and I fixed it by</span>
      </a>
      <a href="" class="ticket">
        <span class="ticket-title">Issue with the Music Player for the homepage on Garage Band</span>
        <span class="ticket-meta">imbradmiller said an hour ago</span>
        <span class="ticket-comment">This is exactly the issue I'm having and I fixed it by</span>
      </a>
    </section>

  </div>

</div>

</body>
</html>
