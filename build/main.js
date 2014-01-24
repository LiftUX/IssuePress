angular.module('ui.breadcrumbs', [])
.factory('breadcrumbs', ['$rootScope', '$location', '$routeParams', function($rootScope, $location, $routeParams){

  // Dashboard bc item
  var dashboard = {
    href: '/dashboard',
    title: 'Dashboard'
  };

  // Sections bc item
  var sections = {
    href: '/sections',
    title: 'Sections'
  };

  // Build BC items based on path
  var setBreadcrumbs = function(){
    var path = $location.path();
    bcArray = [];

    if(path === '/dashboard') {
      bcArray = [dashboard];
    } else if(path === '/sections') {
      bcArray = [dashboard,sections];
    } 
    
    if (typeof $routeParams.repo != 'undefined'){ 
      var repo = $routeParams.repo;
      bcArray = [dashboard,sections, {
        href: '/'+repo,
        title: $routeParams.repo
      }];
      
      // Check if it's a specific issue template
      if (typeof $routeParams.issue != 'undefined'){
        bcArray.push({
          href: path,
          title: "Issue #" + $routeParams.issue
        });

      // Check if it's a Create Issue template
      } else if(path === '/' + repo + '/new') {
        bcArray.push({
          href: path,
          title: "Create Issue"
        });
      }

    }

    return bcArray;
  };

  // Init with 
  var breadcrumbs = setBreadcrumbs();
  var breadcrumbsService = {};
  
  //we want to update breadcrumbs only when a route is actually changed
  //as $location.path() will get updated imediatelly (even if route change fails!)
  $rootScope.$on('$routeChangeSuccess', function(event, current){

    breadcrumbs = setBreadcrumbs();

  });

  breadcrumbsService.getAll = function() {
    return breadcrumbs;
  };

  breadcrumbsService.getFirst = function() {
    return breadcrumbs[0] || {};
  };

  return breadcrumbsService;
}]);

angular.module('ui.gravatar', ['md5'])

.factory('gravatar', ['md5', function(md5){

  var gravatarService = {};

  gravatarService.getEmailHash = function(email) {
    return md5.createHash(email.toLowerCase());
  };

  return gravatarService;

}])

.directive('gravatarImage', function () {
  return {
    restrict:"A",
    replace: true,
    scope: {
      secure: '@secure',
      gravatarHash: '@gravatarHash',
      s: '@s',
      r: '@r',
      d: '@d'
    },
    template: '<img ng-show="src" data-ng-attr-src="{{src}}" />',
    controller: ['$scope', '$element', '$attrs', function($scope, $element, $attrs){

      var emailHash;

      if(typeof $scope.gravatarHash !== 'undefined')
        emailHash = $scope.gravatarHash;
      else
        emailHash = '00000000000000000000000000000000';

      var protocol = $scope.secure ? 'https://secure' : 'http://';
      var src = protocol + 'gravatar.com/avatar/' + emailHash + '?d=mm&';

      if(typeof $scope.s !== 'undefined') 
        src += 's=' + $scope.s + '&';

      if(typeof $scope.r !== 'undefined')  
        src += 'r=' + $scope.r + '&';

      if(typeof $scope.d !== 'undefined')
        src += 'd=' + encodeURIComponent($scope.d);

      $scope.src = src;
    }],

  };
});

angular.module('ui.markdown', [])

.factory('marked', ['$window', function($window) {

  if($window.marked)
    return $window.marked;

}])

.directive('markdown', ['$timeout', 'marked', function($timeout, marked){
  return {
    restrict: 'A',
    replace: true,
    transclude: true,
    scope: {
//      'body': '@body',
    },
    link: function(scope, element, attrs) {
      var timeoutID = $timeout(function() {
        element.html(marked(element.text()));
      }, 1000);
    },
    template: '<div class="rendered-markdown" data-ng-transclude></div>'
  };
}]);


/**
 * marked - a markdown parser
 * Copyright (c) 2011-2013, Christopher Jeffrey. (MIT Licensed)
 * https://github.com/chjj/marked
 */

;(function() {

/**
 * Block-Level Grammar
 */

var block = {
  newline: /^\n+/,
  code: /^( {4}[^\n]+\n*)+/,
  fences: noop,
  hr: /^( *[-*_]){3,} *(?:\n+|$)/,
  heading: /^ *(#{1,6}) *([^\n]+?) *#* *(?:\n+|$)/,
  nptable: noop,
  lheading: /^([^\n]+)\n *(=|-){2,} *(?:\n+|$)/,
  blockquote: /^( *>[^\n]+(\n[^\n]+)*\n*)+/,
  list: /^( *)(bull) [\s\S]+?(?:hr|\n{2,}(?! )(?!\1bull )\n*|\s*$)/,
  html: /^ *(?:comment|closed|closing) *(?:\n{2,}|\s*$)/,
  def: /^ *\[([^\]]+)\]: *<?([^\s>]+)>?(?: +["(]([^\n]+)[")])? *(?:\n+|$)/,
  table: noop,
  paragraph: /^((?:[^\n]+\n?(?!hr|heading|lheading|blockquote|tag|def))+)\n*/,
  text: /^[^\n]+/
};

block.bullet = /(?:[*+-]|\d+\.)/;
block.item = /^( *)(bull) [^\n]*(?:\n(?!\1bull )[^\n]*)*/;
block.item = replace(block.item, 'gm')
  (/bull/g, block.bullet)
  ();

block.list = replace(block.list)
  (/bull/g, block.bullet)
  ('hr', /\n+(?=(?: *[-*_]){3,} *(?:\n+|$))/)
  ();

block._tag = '(?!(?:'
  + 'a|em|strong|small|s|cite|q|dfn|abbr|data|time|code'
  + '|var|samp|kbd|sub|sup|i|b|u|mark|ruby|rt|rp|bdi|bdo'
  + '|span|br|wbr|ins|del|img)\\b)\\w+(?!:/|[^\\w\\s@]*@)\\b';

block.html = replace(block.html)
  ('comment', /<!--[\s\S]*?-->/)
  ('closed', /<(tag)[\s\S]+?<\/\1>/)
  ('closing', /<tag(?:"[^"]*"|'[^']*'|[^'">])*?>/)
  (/tag/g, block._tag)
  ();

block.paragraph = replace(block.paragraph)
  ('hr', block.hr)
  ('heading', block.heading)
  ('lheading', block.lheading)
  ('blockquote', block.blockquote)
  ('tag', '<' + block._tag)
  ('def', block.def)
  ();

/**
 * Normal Block Grammar
 */

block.normal = merge({}, block);

/**
 * GFM Block Grammar
 */

block.gfm = merge({}, block.normal, {
  fences: /^ *(`{3,}|~{3,}) *(\S+)? *\n([\s\S]+?)\s*\1 *(?:\n+|$)/,
  paragraph: /^/
});

block.gfm.paragraph = replace(block.paragraph)
  ('(?!', '(?!'
    + block.gfm.fences.source.replace('\\1', '\\2') + '|'
    + block.list.source.replace('\\1', '\\3') + '|')
  ();

/**
 * GFM + Tables Block Grammar
 */

block.tables = merge({}, block.gfm, {
  nptable: /^ *(\S.*\|.*)\n *([-:]+ *\|[-| :]*)\n((?:.*\|.*(?:\n|$))*)\n*/,
  table: /^ *\|(.+)\n *\|( *[-:]+[-| :]*)\n((?: *\|.*(?:\n|$))*)\n*/
});

/**
 * Block Lexer
 */

function Lexer(options) {
  this.tokens = [];
  this.tokens.links = {};
  this.options = options || marked.defaults;
  this.rules = block.normal;

  if (this.options.gfm) {
    if (this.options.tables) {
      this.rules = block.tables;
    } else {
      this.rules = block.gfm;
    }
  }
}

/**
 * Expose Block Rules
 */

Lexer.rules = block;

/**
 * Static Lex Method
 */

Lexer.lex = function(src, options) {
  var lexer = new Lexer(options);
  return lexer.lex(src);
};

/**
 * Preprocessing
 */

Lexer.prototype.lex = function(src) {
  src = src
    .replace(/\r\n|\r/g, '\n')
    .replace(/\t/g, '    ')
    .replace(/\u00a0/g, ' ')
    .replace(/\u2424/g, '\n');

  return this.token(src, true);
};

/**
 * Lexing
 */

Lexer.prototype.token = function(src, top) {
  var src = src.replace(/^ +$/gm, '')
    , next
    , loose
    , cap
    , bull
    , b
    , item
    , space
    , i
    , l;

  while (src) {
    // newline
    if (cap = this.rules.newline.exec(src)) {
      src = src.substring(cap[0].length);
      if (cap[0].length > 1) {
        this.tokens.push({
          type: 'space'
        });
      }
    }

    // code
    if (cap = this.rules.code.exec(src)) {
      src = src.substring(cap[0].length);
      cap = cap[0].replace(/^ {4}/gm, '');
      this.tokens.push({
        type: 'code',
        text: !this.options.pedantic
          ? cap.replace(/\n+$/, '')
          : cap
      });
      continue;
    }

    // fences (gfm)
    if (cap = this.rules.fences.exec(src)) {
      src = src.substring(cap[0].length);
      this.tokens.push({
        type: 'code',
        lang: cap[2],
        text: cap[3]
      });
      continue;
    }

    // heading
    if (cap = this.rules.heading.exec(src)) {
      src = src.substring(cap[0].length);
      this.tokens.push({
        type: 'heading',
        depth: cap[1].length,
        text: cap[2]
      });
      continue;
    }

    // table no leading pipe (gfm)
    if (top && (cap = this.rules.nptable.exec(src))) {
      src = src.substring(cap[0].length);

      item = {
        type: 'table',
        header: cap[1].replace(/^ *| *\| *$/g, '').split(/ *\| */),
        align: cap[2].replace(/^ *|\| *$/g, '').split(/ *\| */),
        cells: cap[3].replace(/\n$/, '').split('\n')
      };

      for (i = 0; i < item.align.length; i++) {
        if (/^ *-+: *$/.test(item.align[i])) {
          item.align[i] = 'right';
        } else if (/^ *:-+: *$/.test(item.align[i])) {
          item.align[i] = 'center';
        } else if (/^ *:-+ *$/.test(item.align[i])) {
          item.align[i] = 'left';
        } else {
          item.align[i] = null;
        }
      }

      for (i = 0; i < item.cells.length; i++) {
        item.cells[i] = item.cells[i].split(/ *\| */);
      }

      this.tokens.push(item);

      continue;
    }

    // lheading
    if (cap = this.rules.lheading.exec(src)) {
      src = src.substring(cap[0].length);
      this.tokens.push({
        type: 'heading',
        depth: cap[2] === '=' ? 1 : 2,
        text: cap[1]
      });
      continue;
    }

    // hr
    if (cap = this.rules.hr.exec(src)) {
      src = src.substring(cap[0].length);
      this.tokens.push({
        type: 'hr'
      });
      continue;
    }

    // blockquote
    if (cap = this.rules.blockquote.exec(src)) {
      src = src.substring(cap[0].length);

      this.tokens.push({
        type: 'blockquote_start'
      });

      cap = cap[0].replace(/^ *> ?/gm, '');

      // Pass `top` to keep the current
      // "toplevel" state. This is exactly
      // how markdown.pl works.
      this.token(cap, top);

      this.tokens.push({
        type: 'blockquote_end'
      });

      continue;
    }

    // list
    if (cap = this.rules.list.exec(src)) {
      src = src.substring(cap[0].length);
      bull = cap[2];

      this.tokens.push({
        type: 'list_start',
        ordered: bull.length > 1
      });

      // Get each top-level item.
      cap = cap[0].match(this.rules.item);

      next = false;
      l = cap.length;
      i = 0;

      for (; i < l; i++) {
        item = cap[i];

        // Remove the list item's bullet
        // so it is seen as the next token.
        space = item.length;
        item = item.replace(/^ *([*+-]|\d+\.) +/, '');

        // Outdent whatever the
        // list item contains. Hacky.
        if (~item.indexOf('\n ')) {
          space -= item.length;
          item = !this.options.pedantic
            ? item.replace(new RegExp('^ {1,' + space + '}', 'gm'), '')
            : item.replace(/^ {1,4}/gm, '');
        }

        // Determine whether the next list item belongs here.
        // Backpedal if it does not belong in this list.
        if (this.options.smartLists && i !== l - 1) {
          b = block.bullet.exec(cap[i + 1])[0];
          if (bull !== b && !(bull.length > 1 && b.length > 1)) {
            src = cap.slice(i + 1).join('\n') + src;
            i = l - 1;
          }
        }

        // Determine whether item is loose or not.
        // Use: /(^|\n)(?! )[^\n]+\n\n(?!\s*$)/
        // for discount behavior.
        loose = next || /\n\n(?!\s*$)/.test(item);
        if (i !== l - 1) {
          next = item.charAt(item.length - 1) === '\n';
          if (!loose) loose = next;
        }

        this.tokens.push({
          type: loose
            ? 'loose_item_start'
            : 'list_item_start'
        });

        // Recurse.
        this.token(item, false);

        this.tokens.push({
          type: 'list_item_end'
        });
      }

      this.tokens.push({
        type: 'list_end'
      });

      continue;
    }

    // html
    if (cap = this.rules.html.exec(src)) {
      src = src.substring(cap[0].length);
      this.tokens.push({
        type: this.options.sanitize
          ? 'paragraph'
          : 'html',
        pre: cap[1] === 'pre' || cap[1] === 'script' || cap[1] === 'style',
        text: cap[0]
      });
      continue;
    }

    // def
    if (top && (cap = this.rules.def.exec(src))) {
      src = src.substring(cap[0].length);
      this.tokens.links[cap[1].toLowerCase()] = {
        href: cap[2],
        title: cap[3]
      };
      continue;
    }

    // table (gfm)
    if (top && (cap = this.rules.table.exec(src))) {
      src = src.substring(cap[0].length);

      item = {
        type: 'table',
        header: cap[1].replace(/^ *| *\| *$/g, '').split(/ *\| */),
        align: cap[2].replace(/^ *|\| *$/g, '').split(/ *\| */),
        cells: cap[3].replace(/(?: *\| *)?\n$/, '').split('\n')
      };

      for (i = 0; i < item.align.length; i++) {
        if (/^ *-+: *$/.test(item.align[i])) {
          item.align[i] = 'right';
        } else if (/^ *:-+: *$/.test(item.align[i])) {
          item.align[i] = 'center';
        } else if (/^ *:-+ *$/.test(item.align[i])) {
          item.align[i] = 'left';
        } else {
          item.align[i] = null;
        }
      }

      for (i = 0; i < item.cells.length; i++) {
        item.cells[i] = item.cells[i]
          .replace(/^ *\| *| *\| *$/g, '')
          .split(/ *\| */);
      }

      this.tokens.push(item);

      continue;
    }

    // top-level paragraph
    if (top && (cap = this.rules.paragraph.exec(src))) {
      src = src.substring(cap[0].length);
      this.tokens.push({
        type: 'paragraph',
        text: cap[1].charAt(cap[1].length - 1) === '\n'
          ? cap[1].slice(0, -1)
          : cap[1]
      });
      continue;
    }

    // text
    if (cap = this.rules.text.exec(src)) {
      // Top-level should never reach here.
      src = src.substring(cap[0].length);
      this.tokens.push({
        type: 'text',
        text: cap[0]
      });
      continue;
    }

    if (src) {
      throw new
        Error('Infinite loop on byte: ' + src.charCodeAt(0));
    }
  }

  return this.tokens;
};

/**
 * Inline-Level Grammar
 */

var inline = {
  escape: /^\\([\\`*{}\[\]()#+\-.!_>])/,
  autolink: /^<([^ >]+(@|:\/)[^ >]+)>/,
  url: noop,
  tag: /^<!--[\s\S]*?-->|^<\/?\w+(?:"[^"]*"|'[^']*'|[^'">])*?>/,
  link: /^!?\[(inside)\]\(href\)/,
  reflink: /^!?\[(inside)\]\s*\[([^\]]*)\]/,
  nolink: /^!?\[((?:\[[^\]]*\]|[^\[\]])*)\]/,
  strong: /^__([\s\S]+?)__(?!_)|^\*\*([\s\S]+?)\*\*(?!\*)/,
  em: /^\b_((?:__|[\s\S])+?)_\b|^\*((?:\*\*|[\s\S])+?)\*(?!\*)/,
  code: /^(`+)\s*([\s\S]*?[^`])\s*\1(?!`)/,
  br: /^ {2,}\n(?!\s*$)/,
  del: noop,
  text: /^[\s\S]+?(?=[\\<!\[_*`]| {2,}\n|$)/
};

inline._inside = /(?:\[[^\]]*\]|[^\[\]]|\](?=[^\[]*\]))*/;
inline._href = /\s*<?([\s\S]*?)>?(?:\s+['"]([\s\S]*?)['"])?\s*/;

inline.link = replace(inline.link)
  ('inside', inline._inside)
  ('href', inline._href)
  ();

inline.reflink = replace(inline.reflink)
  ('inside', inline._inside)
  ();

/**
 * Normal Inline Grammar
 */

inline.normal = merge({}, inline);

/**
 * Pedantic Inline Grammar
 */

inline.pedantic = merge({}, inline.normal, {
  strong: /^__(?=\S)([\s\S]*?\S)__(?!_)|^\*\*(?=\S)([\s\S]*?\S)\*\*(?!\*)/,
  em: /^_(?=\S)([\s\S]*?\S)_(?!_)|^\*(?=\S)([\s\S]*?\S)\*(?!\*)/
});

/**
 * GFM Inline Grammar
 */

inline.gfm = merge({}, inline.normal, {
  escape: replace(inline.escape)('])', '~|])')(),
  url: /^(https?:\/\/[^\s<]+[^<.,:;"')\]\s])/,
  del: /^~~(?=\S)([\s\S]*?\S)~~/,
  text: replace(inline.text)
    (']|', '~]|')
    ('|', '|https?://|')
    ()
});

/**
 * GFM + Line Breaks Inline Grammar
 */

inline.breaks = merge({}, inline.gfm, {
  br: replace(inline.br)('{2,}', '*')(),
  text: replace(inline.gfm.text)('{2,}', '*')()
});

/**
 * Inline Lexer & Compiler
 */

function InlineLexer(links, options) {
  this.options = options || marked.defaults;
  this.links = links;
  this.rules = inline.normal;
  this.renderer = this.options.renderer || new Renderer;
  this.renderer.options = this.options;

  if (!this.links) {
    throw new
      Error('Tokens array requires a `links` property.');
  }

  if (this.options.gfm) {
    if (this.options.breaks) {
      this.rules = inline.breaks;
    } else {
      this.rules = inline.gfm;
    }
  } else if (this.options.pedantic) {
    this.rules = inline.pedantic;
  }
}

/**
 * Expose Inline Rules
 */

InlineLexer.rules = inline;

/**
 * Static Lexing/Compiling Method
 */

InlineLexer.output = function(src, links, options) {
  var inline = new InlineLexer(links, options);
  return inline.output(src);
};

/**
 * Lexing/Compiling
 */

InlineLexer.prototype.output = function(src) {
  var out = ''
    , link
    , text
    , href
    , cap;

  while (src) {
    // escape
    if (cap = this.rules.escape.exec(src)) {
      src = src.substring(cap[0].length);
      out += cap[1];
      continue;
    }

    // autolink
    if (cap = this.rules.autolink.exec(src)) {
      src = src.substring(cap[0].length);
      if (cap[2] === '@') {
        text = cap[1].charAt(6) === ':'
          ? this.mangle(cap[1].substring(7))
          : this.mangle(cap[1]);
        href = this.mangle('mailto:') + text;
      } else {
        text = escape(cap[1]);
        href = text;
      }
      out += this.renderer.link(href, null, text);
      continue;
    }

    // url (gfm)
    if (cap = this.rules.url.exec(src)) {
      src = src.substring(cap[0].length);
      text = escape(cap[1]);
      href = text;
      out += this.renderer.link(href, null, text);
      continue;
    }

    // tag
    if (cap = this.rules.tag.exec(src)) {
      src = src.substring(cap[0].length);
      out += this.options.sanitize
        ? escape(cap[0])
        : cap[0];
      continue;
    }

    // link
    if (cap = this.rules.link.exec(src)) {
      src = src.substring(cap[0].length);
      out += this.outputLink(cap, {
        href: cap[2],
        title: cap[3]
      });
      continue;
    }

    // reflink, nolink
    if ((cap = this.rules.reflink.exec(src))
        || (cap = this.rules.nolink.exec(src))) {
      src = src.substring(cap[0].length);
      link = (cap[2] || cap[1]).replace(/\s+/g, ' ');
      link = this.links[link.toLowerCase()];
      if (!link || !link.href) {
        out += cap[0].charAt(0);
        src = cap[0].substring(1) + src;
        continue;
      }
      out += this.outputLink(cap, link);
      continue;
    }

    // strong
    if (cap = this.rules.strong.exec(src)) {
      src = src.substring(cap[0].length);
      out += this.renderer.strong(this.output(cap[2] || cap[1]));
      continue;
    }

    // em
    if (cap = this.rules.em.exec(src)) {
      src = src.substring(cap[0].length);
      out += this.renderer.em(this.output(cap[2] || cap[1]));
      continue;
    }

    // code
    if (cap = this.rules.code.exec(src)) {
      src = src.substring(cap[0].length);
      out += this.renderer.codespan(escape(cap[2], true));
      continue;
    }

    // br
    if (cap = this.rules.br.exec(src)) {
      src = src.substring(cap[0].length);
      out += this.renderer.br();
      continue;
    }

    // del (gfm)
    if (cap = this.rules.del.exec(src)) {
      src = src.substring(cap[0].length);
      out += this.renderer.del(this.output(cap[1]));
      continue;
    }

    // text
    if (cap = this.rules.text.exec(src)) {
      src = src.substring(cap[0].length);
      out += escape(this.smartypants(cap[0]));
      continue;
    }

    if (src) {
      throw new
        Error('Infinite loop on byte: ' + src.charCodeAt(0));
    }
  }

  return out;
};

/**
 * Compile Link
 */

InlineLexer.prototype.outputLink = function(cap, link) {
  var href = escape(link.href)
    , title = link.title ? escape(link.title) : null;

  return cap[0].charAt(0) !== '!'
    ? this.renderer.link(href, title, this.output(cap[1]))
    : this.renderer.image(href, title, escape(cap[1]));
};

/**
 * Smartypants Transformations
 */

InlineLexer.prototype.smartypants = function(text) {
  if (!this.options.smartypants) return text;
  return text
    // em-dashes
    .replace(/--/g, '\u2014')
    // opening singles
    .replace(/(^|[-\u2014/(\[{"\s])'/g, '$1\u2018')
    // closing singles & apostrophes
    .replace(/'/g, '\u2019')
    // opening doubles
    .replace(/(^|[-\u2014/(\[{\u2018\s])"/g, '$1\u201c')
    // closing doubles
    .replace(/"/g, '\u201d')
    // ellipses
    .replace(/\.{3}/g, '\u2026');
};

/**
 * Mangle Links
 */

InlineLexer.prototype.mangle = function(text) {
  var out = ''
    , l = text.length
    , i = 0
    , ch;

  for (; i < l; i++) {
    ch = text.charCodeAt(i);
    if (Math.random() > 0.5) {
      ch = 'x' + ch.toString(16);
    }
    out += '&#' + ch + ';';
  }

  return out;
};

/**
 * Renderer
 */

function Renderer(options) {
  this.options = options || {};
}

Renderer.prototype.code = function(code, lang, escaped) {
  if (this.options.highlight) {
    var out = this.options.highlight(code, lang);
    if (out != null && out !== code) {
      escaped = true;
      code = out;
    }
  }

  if (!lang) {
    return '<pre><code>'
      + (escaped ? code : escape(code, true))
      + '\n</code></pre>';
  }

  return '<pre><code class="'
    + this.options.langPrefix
    + escape(lang, true)
    + '">'
    + (escaped ? code : escape(code, true))
    + '\n</code></pre>\n';
};

Renderer.prototype.blockquote = function(quote) {
  return '<blockquote>\n' + quote + '</blockquote>\n';
};

Renderer.prototype.html = function(html) {
  return html;
};

Renderer.prototype.heading = function(text, level, raw) {
  return '<h'
    + level
    + ' id="'
    + this.options.headerPrefix
    + raw.toLowerCase().replace(/[^\w]+/g, '-')
    + '">'
    + text
    + '</h'
    + level
    + '>\n';
};

Renderer.prototype.hr = function() {
  return '<hr>\n';
};

Renderer.prototype.list = function(body, ordered) {
  var type = ordered ? 'ol' : 'ul';
  return '<' + type + '>\n' + body + '</' + type + '>\n';
};

Renderer.prototype.listitem = function(text) {
  return '<li>' + text + '</li>\n';
};

Renderer.prototype.paragraph = function(text) {
  return '<p>' + text + '</p>\n';
};

Renderer.prototype.table = function(header, body) {
  return '<table>\n'
    + '<thead>\n'
    + header
    + '</thead>\n'
    + '<tbody>\n'
    + body
    + '</tbody>\n'
    + '</table>\n';
};

Renderer.prototype.tablerow = function(content) {
  return '<tr>\n' + content + '</tr>\n';
};

Renderer.prototype.tablecell = function(content, flags) {
  var type = flags.header ? 'th' : 'td';
  var tag = flags.align
    ? '<' + type + ' style="text-align:' + flags.align + '">'
    : '<' + type + '>';
  return tag + content + '</' + type + '>\n';
};

// span level renderer
Renderer.prototype.strong = function(text) {
  return '<strong>' + text + '</strong>';
};

Renderer.prototype.em = function(text) {
  return '<em>' + text + '</em>';
};

Renderer.prototype.codespan = function(text) {
  return '<code>' + text + '</code>';
};

Renderer.prototype.br = function() {
  return '<br>';
};

Renderer.prototype.del = function(text) {
  return '<del>' + text + '</del>';
};

Renderer.prototype.link = function(href, title, text) {
  if (this.options.sanitize) {
    try {
      var prot = decodeURIComponent(unescape(href))
        .replace(/[^\w:]/g, '')
        .toLowerCase();
    } catch (e) {
      return '';
    }
    if (prot.indexOf('javascript:') === 0) {
      return '';
    }
  }
  var out = '<a href="' + href + '"';
  if (title) {
    out += ' title="' + title + '"';
  }
  out += '>' + text + '</a>';
  return out;
};

Renderer.prototype.image = function(href, title, text) {
  var out = '<img src="' + href + '" alt="' + text + '"';
  if (title) {
    out += ' title="' + title + '"';
  }
  out += '>';
  return out;
};

/**
 * Parsing & Compiling
 */

function Parser(options) {
  this.tokens = [];
  this.token = null;
  this.options = options || marked.defaults;
  this.options.renderer = this.options.renderer || new Renderer;
  this.renderer = this.options.renderer;
  this.renderer.options = this.options;
}

/**
 * Static Parse Method
 */

Parser.parse = function(src, options, renderer) {
  var parser = new Parser(options, renderer);
  return parser.parse(src);
};

/**
 * Parse Loop
 */

Parser.prototype.parse = function(src) {
  this.inline = new InlineLexer(src.links, this.options, this.renderer);
  this.tokens = src.reverse();

  var out = '';
  while (this.next()) {
    out += this.tok();
  }

  return out;
};

/**
 * Next Token
 */

Parser.prototype.next = function() {
  return this.token = this.tokens.pop();
};

/**
 * Preview Next Token
 */

Parser.prototype.peek = function() {
  return this.tokens[this.tokens.length - 1] || 0;
};

/**
 * Parse Text Tokens
 */

Parser.prototype.parseText = function() {
  var body = this.token.text;

  while (this.peek().type === 'text') {
    body += '\n' + this.next().text;
  }

  return this.inline.output(body);
};

/**
 * Parse Current Token
 */

Parser.prototype.tok = function() {
  switch (this.token.type) {
    case 'space': {
      return '';
    }
    case 'hr': {
      return this.renderer.hr();
    }
    case 'heading': {
      return this.renderer.heading(
        this.inline.output(this.token.text),
        this.token.depth,
        this.token.text);
    }
    case 'code': {
      return this.renderer.code(this.token.text,
        this.token.lang,
        this.token.escaped);
    }
    case 'table': {
      var header = ''
        , body = ''
        , i
        , row
        , cell
        , flags
        , j;

      // header
      cell = '';
      for (i = 0; i < this.token.header.length; i++) {
        flags = { header: true, align: this.token.align[i] };
        cell += this.renderer.tablecell(
          this.inline.output(this.token.header[i]),
          { header: true, align: this.token.align[i] }
        );
      }
      header += this.renderer.tablerow(cell);

      for (i = 0; i < this.token.cells.length; i++) {
        row = this.token.cells[i];

        cell = '';
        for (j = 0; j < row.length; j++) {
          cell += this.renderer.tablecell(
            this.inline.output(row[j]),
            { header: false, align: this.token.align[j] }
          );
        }

        body += this.renderer.tablerow(cell);
      }
      return this.renderer.table(header, body);
    }
    case 'blockquote_start': {
      var body = '';

      while (this.next().type !== 'blockquote_end') {
        body += this.tok();
      }

      return this.renderer.blockquote(body);
    }
    case 'list_start': {
      var body = ''
        , ordered = this.token.ordered;

      while (this.next().type !== 'list_end') {
        body += this.tok();
      }

      return this.renderer.list(body, ordered);
    }
    case 'list_item_start': {
      var body = '';

      while (this.next().type !== 'list_item_end') {
        body += this.token.type === 'text'
          ? this.parseText()
          : this.tok();
      }

      return this.renderer.listitem(body);
    }
    case 'loose_item_start': {
      var body = '';

      while (this.next().type !== 'list_item_end') {
        body += this.tok();
      }

      return this.renderer.listitem(body);
    }
    case 'html': {
      var html = !this.token.pre && !this.options.pedantic
        ? this.inline.output(this.token.text)
        : this.token.text;
      return this.renderer.html(html);
    }
    case 'paragraph': {
      return this.renderer.paragraph(this.inline.output(this.token.text));
    }
    case 'text': {
      return this.renderer.paragraph(this.parseText());
    }
  }
};

/**
 * Helpers
 */

function escape(html, encode) {
  return html
    .replace(!encode ? /&(?!#?\w+;)/g : /&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;');
}

function unescape(html) {
  return html.replace(/&([#\w]+);/g, function(_, n) {
    n = n.toLowerCase();
    if (n === 'colon') return ':';
    if (n.charAt(0) === '#') {
      return n.charAt(1) === 'x'
        ? String.fromCharCode(parseInt(n.substring(2), 16))
        : String.fromCharCode(+n.substring(1));
    }
    return '';
  });
}

function replace(regex, opt) {
  regex = regex.source;
  opt = opt || '';
  return function self(name, val) {
    if (!name) return new RegExp(regex, opt);
    val = val.source || val;
    val = val.replace(/(^|[^\[])\^/g, '$1');
    regex = regex.replace(name, val);
    return self;
  };
}

function noop() {}
noop.exec = noop;

function merge(obj) {
  var i = 1
    , target
    , key;

  for (; i < arguments.length; i++) {
    target = arguments[i];
    for (key in target) {
      if (Object.prototype.hasOwnProperty.call(target, key)) {
        obj[key] = target[key];
      }
    }
  }

  return obj;
}


/**
 * Marked
 */

function marked(src, opt, callback) {
  if (callback || typeof opt === 'function') {
    if (!callback) {
      callback = opt;
      opt = null;
    }

    opt = merge({}, marked.defaults, opt || {});

    var highlight = opt.highlight
      , tokens
      , pending
      , i = 0;

    try {
      tokens = Lexer.lex(src, opt)
    } catch (e) {
      return callback(e);
    }

    pending = tokens.length;

    var done = function() {
      var out, err;

      try {
        out = Parser.parse(tokens, opt);
      } catch (e) {
        err = e;
      }

      opt.highlight = highlight;

      return err
        ? callback(err)
        : callback(null, out);
    };

    if (!highlight || highlight.length < 3) {
      return done();
    }

    delete opt.highlight;

    if (!pending) return done();

    for (; i < tokens.length; i++) {
      (function(token) {
        if (token.type !== 'code') {
          return --pending || done();
        }
        return highlight(token.text, token.lang, function(err, code) {
          if (code == null || code === token.text) {
            return --pending || done();
          }
          token.text = code;
          token.escaped = true;
          --pending || done();
        });
      })(tokens[i]);
    }

    return;
  }
  try {
    if (opt) opt = merge({}, marked.defaults, opt);
    return Parser.parse(Lexer.lex(src, opt), opt);
  } catch (e) {
    e.message += '\nPlease report this to https://github.com/chjj/marked.';
    if ((opt || marked.defaults).silent) {
      return '<p>An error occured:</p><pre>'
        + escape(e.message + '', true)
        + '</pre>';
    }
    throw e;
  }
}

/**
 * Options
 */

marked.options =
marked.setOptions = function(opt) {
  merge(marked.defaults, opt);
  return marked;
};

marked.defaults = {
  gfm: true,
  tables: true,
  breaks: false,
  pedantic: false,
  sanitize: false,
  smartLists: false,
  silent: false,
  highlight: null,
  langPrefix: 'lang-',
  smartypants: false,
  headerPrefix: '',
  renderer: new Renderer
};

/**
 * Expose
 */

marked.Parser = Parser;
marked.parser = Parser.parse;

marked.Renderer = Renderer;

marked.Lexer = Lexer;
marked.lexer = Lexer.lex;

marked.InlineLexer = InlineLexer;
marked.inlineLexer = InlineLexer.output;

marked.parse = marked;

if (typeof exports === 'object') {
  module.exports = marked;
} else if (typeof define === 'function' && define.amd) {
  define(function() { return marked; });
} else {
  this.marked = marked;
}

}).call(function() {
  return this || (typeof window !== 'undefined' ? window : global);
}());

// http://kevin.vanzonneveld.net
// +   original by: Webtoolkit.info (http://www.webtoolkit.info/)
// + namespaced by: Michael White (http://getsprink.com)
// +    tweaked by: Jack
// +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
// +      input by: Brett Zamir (http://brett-zamir.me)
// +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
// -    depends on: utf8_encode
// *     example 1: md5('Kevin van Zonneveld');
// *     returns 1: '6e658d4bfcb59cc13f96c14450ac40b9'
// Adapted to AngularJS Service by: Jim Lavin (http://jimlavin.net)
// after injecting into your controller, directive or service
// *     example 1: md5.createHash('Kevin van Zonneveld');
// *     returns 1: '6e658d4bfcb59cc13f96c14450ac40b9'

angular.module('md5', []).factory('md5', [function() {

        var md5 = {

            createHash: function(str) {

                var xl;

                var rotateLeft = function (lValue, iShiftBits) {
                    return (lValue << iShiftBits) | (lValue >>> (32 - iShiftBits));
                };

                var addUnsigned = function (lX, lY) {
                    var lX4, lY4, lX8, lY8, lResult;
                    lX8 = (lX & 0x80000000);
                    lY8 = (lY & 0x80000000);
                    lX4 = (lX & 0x40000000);
                    lY4 = (lY & 0x40000000);
                    lResult = (lX & 0x3FFFFFFF) + (lY & 0x3FFFFFFF);
                    if (lX4 & lY4) {
                        return (lResult ^ 0x80000000 ^ lX8 ^ lY8);
                    }
                    if (lX4 | lY4) {
                        if (lResult & 0x40000000) {
                            return (lResult ^ 0xC0000000 ^ lX8 ^ lY8);
                        } else {
                            return (lResult ^ 0x40000000 ^ lX8 ^ lY8);
                        }
                    } else {
                        return (lResult ^ lX8 ^ lY8);
                    }
                };

                var _F = function (x, y, z) {
                    return (x & y) | ((~x) & z);
                };
                var _G = function (x, y, z) {
                    return (x & z) | (y & (~z));
                };
                var _H = function (x, y, z) {
                    return (x ^ y ^ z);
                };
                var _I = function (x, y, z) {
                    return (y ^ (x | (~z)));
                };

                var _FF = function (a, b, c, d, x, s, ac) {
                    a = addUnsigned(a, addUnsigned(addUnsigned(_F(b, c, d), x), ac));
                    return addUnsigned(rotateLeft(a, s), b);
                };

                var _GG = function (a, b, c, d, x, s, ac) {
                    a = addUnsigned(a, addUnsigned(addUnsigned(_G(b, c, d), x), ac));
                    return addUnsigned(rotateLeft(a, s), b);
                };

                var _HH = function (a, b, c, d, x, s, ac) {
                    a = addUnsigned(a, addUnsigned(addUnsigned(_H(b, c, d), x), ac));
                    return addUnsigned(rotateLeft(a, s), b);
                };

                var _II = function (a, b, c, d, x, s, ac) {
                    a = addUnsigned(a, addUnsigned(addUnsigned(_I(b, c, d), x), ac));
                    return addUnsigned(rotateLeft(a, s), b);
                };

                var convertToWordArray = function (str) {
                    var lWordCount;
                    var lMessageLength = str.length;
                    var lNumberOfWords_temp1 = lMessageLength + 8;
                    var lNumberOfWords_temp2 = (lNumberOfWords_temp1 - (lNumberOfWords_temp1 % 64)) / 64;
                    var lNumberOfWords = (lNumberOfWords_temp2 + 1) * 16;
                    var lWordArray = new Array(lNumberOfWords - 1);
                    var lBytePosition = 0;
                    var lByteCount = 0;
                    while (lByteCount < lMessageLength) {
                        lWordCount = (lByteCount - (lByteCount % 4)) / 4;
                        lBytePosition = (lByteCount % 4) * 8;
                        lWordArray[lWordCount] = (lWordArray[lWordCount] | (str.charCodeAt(lByteCount) << lBytePosition));
                        lByteCount++;
                    }
                    lWordCount = (lByteCount - (lByteCount % 4)) / 4;
                    lBytePosition = (lByteCount % 4) * 8;
                    lWordArray[lWordCount] = lWordArray[lWordCount] | (0x80 << lBytePosition);
                    lWordArray[lNumberOfWords - 2] = lMessageLength << 3;
                    lWordArray[lNumberOfWords - 1] = lMessageLength >>> 29;
                    return lWordArray;
                };

                var wordToHex = function (lValue) {
                    var wordToHexValue = "",
                        wordToHexValue_temp = "",
                        lByte, lCount;
                    for (lCount = 0; lCount <= 3; lCount++) {
                        lByte = (lValue >>> (lCount * 8)) & 255;
                        wordToHexValue_temp = "0" + lByte.toString(16);
                        wordToHexValue = wordToHexValue + wordToHexValue_temp.substr(wordToHexValue_temp.length - 2, 2);
                    }
                    return wordToHexValue;
                };

                var x = [],
                    k, AA, BB, CC, DD, a, b, c, d, S11 = 7,
                    S12 = 12,
                    S13 = 17,
                    S14 = 22,
                    S21 = 5,
                    S22 = 9,
                    S23 = 14,
                    S24 = 20,
                    S31 = 4,
                    S32 = 11,
                    S33 = 16,
                    S34 = 23,
                    S41 = 6,
                    S42 = 10,
                    S43 = 15,
                    S44 = 21;

                //str = this.utf8_encode(str);
                x = convertToWordArray(str);
                a = 0x67452301;
                b = 0xEFCDAB89;
                c = 0x98BADCFE;
                d = 0x10325476;

                xl = x.length;
                for (k = 0; k < xl; k += 16) {
                    AA = a;
                    BB = b;
                    CC = c;
                    DD = d;
                    a = _FF(a, b, c, d, x[k + 0], S11, 0xD76AA478);
                    d = _FF(d, a, b, c, x[k + 1], S12, 0xE8C7B756);
                    c = _FF(c, d, a, b, x[k + 2], S13, 0x242070DB);
                    b = _FF(b, c, d, a, x[k + 3], S14, 0xC1BDCEEE);
                    a = _FF(a, b, c, d, x[k + 4], S11, 0xF57C0FAF);
                    d = _FF(d, a, b, c, x[k + 5], S12, 0x4787C62A);
                    c = _FF(c, d, a, b, x[k + 6], S13, 0xA8304613);
                    b = _FF(b, c, d, a, x[k + 7], S14, 0xFD469501);
                    a = _FF(a, b, c, d, x[k + 8], S11, 0x698098D8);
                    d = _FF(d, a, b, c, x[k + 9], S12, 0x8B44F7AF);
                    c = _FF(c, d, a, b, x[k + 10], S13, 0xFFFF5BB1);
                    b = _FF(b, c, d, a, x[k + 11], S14, 0x895CD7BE);
                    a = _FF(a, b, c, d, x[k + 12], S11, 0x6B901122);
                    d = _FF(d, a, b, c, x[k + 13], S12, 0xFD987193);
                    c = _FF(c, d, a, b, x[k + 14], S13, 0xA679438E);
                    b = _FF(b, c, d, a, x[k + 15], S14, 0x49B40821);
                    a = _GG(a, b, c, d, x[k + 1], S21, 0xF61E2562);
                    d = _GG(d, a, b, c, x[k + 6], S22, 0xC040B340);
                    c = _GG(c, d, a, b, x[k + 11], S23, 0x265E5A51);
                    b = _GG(b, c, d, a, x[k + 0], S24, 0xE9B6C7AA);
                    a = _GG(a, b, c, d, x[k + 5], S21, 0xD62F105D);
                    d = _GG(d, a, b, c, x[k + 10], S22, 0x2441453);
                    c = _GG(c, d, a, b, x[k + 15], S23, 0xD8A1E681);
                    b = _GG(b, c, d, a, x[k + 4], S24, 0xE7D3FBC8);
                    a = _GG(a, b, c, d, x[k + 9], S21, 0x21E1CDE6);
                    d = _GG(d, a, b, c, x[k + 14], S22, 0xC33707D6);
                    c = _GG(c, d, a, b, x[k + 3], S23, 0xF4D50D87);
                    b = _GG(b, c, d, a, x[k + 8], S24, 0x455A14ED);
                    a = _GG(a, b, c, d, x[k + 13], S21, 0xA9E3E905);
                    d = _GG(d, a, b, c, x[k + 2], S22, 0xFCEFA3F8);
                    c = _GG(c, d, a, b, x[k + 7], S23, 0x676F02D9);
                    b = _GG(b, c, d, a, x[k + 12], S24, 0x8D2A4C8A);
                    a = _HH(a, b, c, d, x[k + 5], S31, 0xFFFA3942);
                    d = _HH(d, a, b, c, x[k + 8], S32, 0x8771F681);
                    c = _HH(c, d, a, b, x[k + 11], S33, 0x6D9D6122);
                    b = _HH(b, c, d, a, x[k + 14], S34, 0xFDE5380C);
                    a = _HH(a, b, c, d, x[k + 1], S31, 0xA4BEEA44);
                    d = _HH(d, a, b, c, x[k + 4], S32, 0x4BDECFA9);
                    c = _HH(c, d, a, b, x[k + 7], S33, 0xF6BB4B60);
                    b = _HH(b, c, d, a, x[k + 10], S34, 0xBEBFBC70);
                    a = _HH(a, b, c, d, x[k + 13], S31, 0x289B7EC6);
                    d = _HH(d, a, b, c, x[k + 0], S32, 0xEAA127FA);
                    c = _HH(c, d, a, b, x[k + 3], S33, 0xD4EF3085);
                    b = _HH(b, c, d, a, x[k + 6], S34, 0x4881D05);
                    a = _HH(a, b, c, d, x[k + 9], S31, 0xD9D4D039);
                    d = _HH(d, a, b, c, x[k + 12], S32, 0xE6DB99E5);
                    c = _HH(c, d, a, b, x[k + 15], S33, 0x1FA27CF8);
                    b = _HH(b, c, d, a, x[k + 2], S34, 0xC4AC5665);
                    a = _II(a, b, c, d, x[k + 0], S41, 0xF4292244);
                    d = _II(d, a, b, c, x[k + 7], S42, 0x432AFF97);
                    c = _II(c, d, a, b, x[k + 14], S43, 0xAB9423A7);
                    b = _II(b, c, d, a, x[k + 5], S44, 0xFC93A039);
                    a = _II(a, b, c, d, x[k + 12], S41, 0x655B59C3);
                    d = _II(d, a, b, c, x[k + 3], S42, 0x8F0CCC92);
                    c = _II(c, d, a, b, x[k + 10], S43, 0xFFEFF47D);
                    b = _II(b, c, d, a, x[k + 1], S44, 0x85845DD1);
                    a = _II(a, b, c, d, x[k + 8], S41, 0x6FA87E4F);
                    d = _II(d, a, b, c, x[k + 15], S42, 0xFE2CE6E0);
                    c = _II(c, d, a, b, x[k + 6], S43, 0xA3014314);
                    b = _II(b, c, d, a, x[k + 13], S44, 0x4E0811A1);
                    a = _II(a, b, c, d, x[k + 4], S41, 0xF7537E82);
                    d = _II(d, a, b, c, x[k + 11], S42, 0xBD3AF235);
                    c = _II(c, d, a, b, x[k + 2], S43, 0x2AD7D2BB);
                    b = _II(b, c, d, a, x[k + 9], S44, 0xEB86D391);
                    a = addUnsigned(a, AA);
                    b = addUnsigned(b, BB);
                    c = addUnsigned(c, CC);
                    d = addUnsigned(d, DD);
                }

                var temp = wordToHex(a) + wordToHex(b) + wordToHex(c) + wordToHex(d);

                return temp.toLowerCase();
            }

        };

        return md5;

    }]);

angular.module('ui.timeago', [])

.filter('timeago', function(){
  // Thanks http://jsfiddle.net/i_woody/cnL5T/
  return function(input, p_allowFuture) {
    var substitute = function (stringOrFunction, number, strings) {
      var string = angular.isFunction(stringOrFunction) ? stringOrFunction(number, dateDifference) : stringOrFunction;
      var value = (strings.numbers && strings.numbers[number]) || number;
      return string.replace(/%d/i, value);
    },
    nowTime = (new Date()).getTime(),
    date = (new Date(input)).getTime(),
    //refreshMillis= 6e4, //A minute
    allowFuture = p_allowFuture || false,
    strings= {
      prefixAgo: null,
      prefixFromNow: null,
      suffixAgo: "ago",
      suffixFromNow: "from now",
      seconds: "less than a minute",
      minute: "about a minute",
      minutes: "%d minutes",
      hour: "about an hour",
      hours: "about %d hours",
      day: "a day",
      days: "%d days",
      month: "about a month",
      months: "%d months",
      year: "about a year",
      years: "%d years"
    },
    dateDifference = nowTime - date,
    words,
    seconds = Math.abs(dateDifference) / 1000,
    minutes = seconds / 60,
    hours = minutes / 60,
    days = hours / 24,
    years = days / 365,
    separator = strings.wordSeparator === undefined ?  " " : strings.wordSeparator,

    // var strings = this.settings.strings;
    prefix = strings.prefixAgo,
    suffix = strings.suffixAgo;

    if (allowFuture) {
      if (dateDifference < 0) {
        prefix = strings.prefixFromNow;
        suffix = strings.suffixFromNow;
      }
    }

    words = seconds < 45 && substitute(strings.seconds, Math.round(seconds), strings) ||
      seconds < 90 && substitute(strings.minute, 1, strings) ||
      minutes < 45 && substitute(strings.minutes, Math.round(minutes), strings) ||
      minutes < 90 && substitute(strings.hour, 1, strings) ||
      hours < 24 && substitute(strings.hours, Math.round(hours), strings) ||
      hours < 42 && substitute(strings.day, 1, strings) ||
      days < 30 && substitute(strings.days, Math.round(days), strings) ||
      days < 45 && substitute(strings.month, 1, strings) ||
      days < 365 && substitute(strings.months, Math.round(days / 30), strings) ||
      years < 1.5 && substitute(strings.year, 1, strings) ||
      substitute(strings.years, Math.round(years), strings);

    return [prefix, words, suffix].join(separator);
  }
});

angular.module('AppState', [])

.factory('IPAppState', function(){
  var appState = window.IP_Vars;
  return appState;
})


.factory('IPData', ['$q', 'IPAppState', 'IPAPI', function($q, IPAppState, IPAPI){


  var data = IPAppState.data;

  var IPData = {},
      repoData = {},
      issueData = {};

  var hasOwnProperty = Object.prototype.hasOwnProperty,
      isEmpty = function(obj) {

        if (obj === null) return true;
        if (obj.length > 0)    return false;
        if (obj.length === 0)  return true;

        for (var key in obj) {
          if (hasOwnProperty.call(obj, key)) return false;
        }

        return true;
      };


  IPData.getRepoData = function(repo){

    repoData = data[repo];

    var keys = ['activity', 'issues', 'repo'];
    var keyTrack = [false, false, false];

    // Loop through cache for each key, check for valid content
    keys.forEach(function(e, i, a){
      if(!isEmpty(repoData[e])) {
        keyTrack[i] = true;
      }
    });

    if(keyTrack[0] && keyTrack[1] && keyTrack[2]){
      console.log("USING CACHED DATA");
      var cachedData = $q.defer();
      cachedData.resolve(repoData);

      return cachedData.promise;
    } else {
      console.log("FETCHING NEW DATA");

      return IPAPI.repo(repo).then(function(result){
        return result.data;
      });
    }

  };

  IPData.getIssueData = function(repo, issue){
    console.log("Looking for issue data for: " + issue + " in " + repo);

    var issues = data[repo].issues;
    var hasIssueCached = false;

    issues.forEach(function(e, i, a){
      if(issue == e.number)
        hasIssueCached = i;
    });

    if(hasIssueCached !== false) {

      console.log("Using Cached Data");
      var cachedData = {};
      cachedData.issue = data[repo].issues[hasIssueCached];
      cachedData.comments = data[repo].comments[issue];

      var cache = $q.defer();
      cache.resolve(cachedData);

      return cache.promise;

    } else {

      console.log("Fetching Fresh Data");
      return IPAPI.issue(repo, issue).then(function(result){
        return result.data;
      });

    }

    

  };

  return IPData;

}])


.factory('IPAPI', ['$http', 'IPAppState', function($http, IPAppState){
  
  var ipUrl = IPAppState.API_PATH;

  var api = {
    repo: function(repo){
      return $http.get(ipUrl + repo).then(function(result) {
        return result.data; 
      });
    },

    issue: function(repo, issue) {
      return $http.get(ipUrl + repo + '/' + issue).then(function(result) { 
        return result.data; 
      });
    },
  
  };

  return api;

}]);


angular.module('components.breadcrumbs', ['ui.breadcrumbs']).directive('ipBreadcrumbs', function(breadcrumbs) {
  return {
    restrict: 'A',
    replace: true,
    templateUrl: IP_PATH + '/app/components/breadcrumbs/breadcrumbs.tpl.html',
    controller: function($scope, breadcrumbs) {
      $scope.breadcrumbs = breadcrumbs.getAll();
    }
  };
});



angular.module('components.issueThread', ['AppState', 'user', 'ui.markdown'])


.directive('ipIssueComment', ['marked', function(marked) {
  return {
    restrict: 'A',
    scope: {
      'title': '@ctitle',
      'body': '@body',
      'author': '@author',
      'action': '@action',
      'timeago': '@timeago',
      'meta': '@meta',
      'follow': '@follow',
      'labels': '@labels',
      'gravatarHash': '@gravatarHash',
    },
    controller: ['$scope', '$element', '$attrs', 'IPAppState', function($scope, $element, $attrs, IPAppState) {
      if($scope.author !== IPAppState.Auth_user)
         $scope.staff = true;
    }],
    templateUrl: IP_PATH + '/app/components/issue-thread/issue-comment.tpl.html'
  };
}])

.directive('ipIssueForm', function() {
  return {
    restrict: 'A',
    replace: true,
    scope: {},
    controller: ['$scope', function($scope) {
      $scope.user = $scope.$parent.user;
      $scope.login_link = $scope.$parent.login_link;
    }],
    templateUrl: IP_PATH + '/app/components/issue-thread/issue-form.tpl.html'
  };
});


angular.module('components.message', []).directive('ipMessage', function() {
  return {
    restrict: 'A',
    replace: true,
    transclude: true,
    scope: {
      'title': '@title'
    },
    template: 
      '<section class="message">' +
        '<div class="message-title">{{title}}</div>' +
        '<div class="message-content" data-ng-transclude></div>' + 
      '</section>'
  };
});


angular.module('components.recentActivity', [])

.directive('ipRecentActivity', function() {
  return {
    restrict: 'A',
    replace: true,
    transclude: true,
    scope: {
      'title': '@title',
    },
    templateUrl: IP_PATH + '/app/components/recent-activity/recent-activity.tpl.html'
  };
})

.directive('ipRecentActivityItem', function() {
  return {
    restrict: 'A',
    replace: true,
    transclude: true,
    scope: {
      'icon': '@icon',
      'timeago': '@timeago',
      'href': '@href'
    },
    templateUrl: IP_PATH + '/app/components/recent-activity/recent-activity-item.tpl.html'
  };
})

.directive('ipRecentActivityItemTitle', function(){
  return {
    restrict: 'A',
    replace: true,
    transclude: true,
    scope: {
      'href': '@href',
    },
    template: '<a href="{{href}}" class="recent-activity-title"><div data-ng-transclude></div></a>'
  };
})


.directive('ipRecentActivityItemMeta', function(){
  return {
    restrict: 'A',
    replace: true,
    transclude: true,
    scope: {
    },
    template: '<div class="recent-activity-meta"><div data-ng-transclude></div></div>'
  };
});

angular.module('components.release', []).directive('ipRelease', function() {
  return {
    restrict: 'A',
    replace: true,
    scope: {
      'title': '@title',
    },
    templateUrl: IP_PATH + '/app/components/release/release.tpl.html',
    controller: function($scope) {
      $scope.release = {
        title: "Example Release Title",
        href: "https://github.com/LiftUX/IssuePress/releases/tag/v-0.1",
        date: "2013-08-24T12:36:13-07:00",
      };
    }
  };
});



angular.module('components.search', []).directive('ipSearch', function() {
  return {
    restrict: 'A',
    replace: true,
    scope: {
    },
    templateUrl: IP_PATH + '/app/components/search/search.tpl.html',
//    controller: function($scope, ipData) {
//      $scope.sections = ipData.sections.getAll();
//    }
  };
});

angular.module('components.sections', []).directive('ipSections', function() {
  return {
    restrict: 'A',
    replace: true,
    scope: {
      'title': '@title',
    },
    templateUrl: IP_PATH + '/app/components/sections/sections.tpl.html',
//    controller: function($scope, ipData) {
//      $scope.sections = ipData.sections.getAll();
//    }
  };
});



angular.module('components.ticketList', [])

.directive('ipTicketList', function() {
  return {
    restrict: 'A',
    replace: true,
    transclude: true,
    scope: {
      'title': '@title',
    },
    templateUrl: IP_PATH + '/app/components/ticket-list/ticket-list.tpl.html'
  };
})

.directive('ipTicketListItem', function() {
  return {
    restrict: 'A',
    replace: true,
    transclude: true,
    scope: {
      'title': '@title',
      'meta': '@meta',
      'comment': '@comment',
      'href': '@href'
    },
    templateUrl: IP_PATH + '/app/components/ticket-list/ticket-list-item.tpl.html'
  };
});


angular.module('create-issue', [])

.controller('CreateIssueCtrl', ['$scope', '$location', '$routeParams', function($scope, $location, $routeParams) {
  
  console.log($location.path());

  console.log($routeParams);
}]);

angular.module('dashboard', [])

.controller('DashboardCtrl', ['$scope', '$location', '$routeParams', function($scope, $location, $routeParams) {
  
  //console.log($location.path());
  //console.log($routeParams);

}]);


angular.module('header', ['user'])

.directive('ipHeader', function() {
  return {
    restrict: 'A',
    replace: true,
    templateUrl: IP_PATH + '/app/header/header.tpl.html',
  };
})

.controller('HeaderCtrl', ['$scope', '$location', 'IPUser',
function ($scope, $location, IPUser) {
  $scope.user = IPUser.user;
  $scope.login_link = IPUser.login_link;
  $scope.logout_link = IPUser.logout_link;

  $scope.loc = $location.$$url;

  $scope.isNavbarActive = function (navBarPath) {
    return navBarPath === $scope.loc;
  };
}]);


angular.module('issue', ['AppState', 'user'])

.controller('IssueCtrl', ['$scope', '$location', '$routeParams', '$http', 'IPAppState', 'IPData', 'IPUser',
function($scope, $location, $routeParams, $http, IPAppState, IPData, IPUser) {

  $scope.user = IPUser.user;
  $scope.login_link = IPUser.login_link;
  $scope.logout_link = IPUser.logout_link;

  $scope.isStaff = function(login) {
    if(login !== IPAppState.Auth_user)
      return true;
    else
      return false;
  };


  $scope.issue = {};
  $scope.comments = [];


  // Set Data for this Scope from IPData service - fetch from cache, or from API otherwise
  IPData.getIssueData($routeParams.repo, $routeParams.issue).then(function(data){
    console.log(data);
    $scope.issue = data.issue;
    $scope.comments = data.comments;
  });

  
}]);

var IP = angular.module('issuepress', [
  'ngRoute',
  'header',
  'dashboard',
  'sections',
  'repo',
  'issue',
  'create-issue',
  'components.search',
  'components.message',
  'components.sections',
  'components.release',
  'components.breadcrumbs',
  'components.recentActivity',
  'components.ticketList',
  'components.issueThread',
  'ui.gravatar',
  'ui.timeago',
  'ui.markdown',
]);


IP.config(function($routeProvider, $locationProvider) {
  $routeProvider
    .when('/dashboard', {
      templateUrl: IP_PATH + '/app/dashboard/dashboard.tpl.html',
    })
    .when('/sections', {
      templateUrl: IP_PATH + '/app/sections/sections.tpl.html',
    })
    .when('/:repo', {
      templateUrl: IP_PATH + '/app/repo/repo.tpl.html',
    })
    .when('/:repo/new', {
      templateUrl: IP_PATH + '/app/create-issue/create-issue.tpl.html',
    })
    .when('/:repo/:issue', {
      templateUrl: IP_PATH + '/app/issue/issue.tpl.html',
    })
    .otherwise({
      redirectTo: "/dashboard"
    });
});

IP.run(function($rootScope, $templateCache) {
  $rootScope.$on('$routeChangeSuccess', function(scope, current, pre) {

    if(!current.loadedTemplateUrl)
      return;

    var tpl = current.loadedTemplateUrl.split("/");
    var tplPart = tpl[tpl.length - 1];
    $rootScope.sidebar = tplPart;
  });
});



angular.module('repo', ['AppState'])

.controller('RepoCtrl', ['$scope', '$location', '$routeParams', '$http', 'IPData', function($scope, $location, $routeParams, $http, IPData) {
  
  $scope.repo = $routeParams.repo;

  // Call to IPData service to populate data
  // Checks Cache before making an API call
  IPData.getRepoData($scope.repo).then(function(data){
    console.log(data);
    $scope.issues = data.issues;
    $scope.activity = data.activity;
  });


}]);


angular.module('sections', ['AppState'])

.controller('SectionsCtrl', ['$scope', '$location', 'IPAppState', function($scope, $location, IPAppState) {
  
  if(IPAppState.repos !== 'undefined')
    $scope.sections = IPAppState.repos;

}]);

angular.module('user', [
  'AppState',
  'ui.gravatar',
])

.factory('IPUser', ['IPAppState', 'gravatar', function(IPAppState, gravatar){

  user = {};
  user.data = IPAppState.user;
  user.login_link = IPAppState.login;
  user.logout_link = IPAppState.logout;

  if(IPAppState.user)
    user.data.gravatar_id = gravatar.getEmailHash(IPAppState.user.email);

  logout = function() {
    console.log(user.data);
    console.log("logged out");
    user.data = null;
    console.log(user.data);
  };

  return { 
    user: user.data,
    login_link: user.login_link,
    logout_link: user.logout_link,
    logout: function(c_user){
      return logout(c_user); 
    }
  };
}]);

