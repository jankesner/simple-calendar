/******************************************************
 * PATTERN LAB NODE
 * EDITION-NODE-GULP
 * The gulp wrapper around patternlab-node core, providing tasks to interact with the core library.
 ******************************************************/
const gulp = require('gulp');
const argv = require('minimist')(process.argv.slice(2));
const path = require('path');
/******************************************************
 * PATTERN LAB  NODE WRAPPER TASKS with core library
 ******************************************************/
const config = require('./patternlab-config.json');
const patternlab = require('@pattern-lab/core')(config);
const browserSync = require('browser-sync').create();
const gulpSass = require("gulp-sass");
const nodeSass = require("node-sass");
const sass = gulpSass(nodeSass);

function build() {
  return patternlab
    .build({
      watch: true,
      cleanPublic: config.cleanPublic,
    })
    .then(() => {
      // do something else when this promise resolves
    });
}

function serve() {
  return patternlab.server
    .serve({
      cleanPublic: config.cleanPublic,
      watch: true,
    })
    .then(() => {
      // do something else when this promise resolves
    });
}

gulp.task('patternlab:version', function () {
  console.log(patternlab.version());
});

gulp.task('patternlab:patternsonly', function () {
  patternlab.patternsonly(config.cleanPublic);
});

gulp.task('patternlab:liststarterkits', function () {
  patternlab.liststarterkits();
});

gulp.task('patternlab:loadstarterkit', function () {
  patternlab.loadstarterkit(argv.kit, argv.clean);
});

gulp.task('patternlab:build', function () {
  build().then(() => {
    // do something else when this promise resolves
  });
});

gulp.task('patternlab:serve', function () {
  serve().then(() => {
    // do something else when this promise resolves
  });
});


/******************************************************
 * SERVER AND WATCH TASKS
******************************************************/
// watch task utility functions
function getSupportedTemplateExtensions() {
  var engines = require('./node_modules/patternlab-node/core/lib/pattern_engines');
  return engines.getSupportedFileExtensions();
}
function getTemplateWatches() {
  return getSupportedTemplateExtensions().map(function (dotExtension) {
    return normalizePath(paths().source.patterns, '**', '*' + dotExtension);
  });
}

/**
 * Reloads BrowserSync.
 * Note: Exits more reliably when used with a done callback.
 */
function reload(done) {
  browserSync.reload();
  done();
}

/**
 * Reloads BrowserSync, with CSS injection.
 * Note: Exits more reliably when used with a done callback.
 */
function reloadCSS(done) {
  browserSync.reload('*.css');
  done();
}

function paths() {
  return config.paths;
}

/**
 * Normalize all paths to be plain, paths with no leading './',
 * relative to the process root, and with backslashes converted to
 * forward slashes. Should work regardless of how the path was
 * written. Accepts any number of parameters, and passes them along to
 * path.resolve().
 *
 * This is intended to avoid all known limitations of gulp.watch().
 *
 * @param {...string} pathFragment - A directory, filename, or glob.
*/
function normalizePath() {
  return path
    .relative(
      process.cwd(),
      path.resolve.apply(this, arguments)
    )
    .replace(/\\/g, "/");
}

function watch() {
  console.log("watch()", paths());
  const watchers = [
    {
      name: 'CSS',
      paths: [normalizePath("source/_patterns/", '**', '*.scss')],
      config: { awaitWriteFinish: true },
      tasks: gulp.series("patternlab:build", reloadCSS)
    }/*,
    {
      name: 'Pattern Scaffolding CSS',
      paths: [normalizePath(paths().source.css, 'pattern-scaffolding.css')],
      config: { awaitWriteFinish: true },
      tasks: gulp.series('pl-copy:css', reloadCSS)
    },
    {
      name: 'Icons',
      paths: [normalizePath(paths().source.icons, '**', '*.svg')],
      config: { awaitWriteFinish: true },
      tasks: gulp.series('svg-sprite')
    },
    {
      name: 'JavaScript',
      paths: [normalizePath(paths().source.js, '**', '*.js')],
      config: { awaitWriteFinish: true },
      tasks: gulp.series('concat-and-minify')
    },
    {
      name: 'Styleguide Files',
      paths: [normalizePath(paths().source.styleguide, '**', '*')],
      config: { awaitWriteFinish: true },
      tasks: gulp.series('pl-copy:styleguide', 'pl-copy:styleguide-css', reloadCSS)
    }*/,
    {
      name: 'Source Files',
      paths: [
        // normalizePath(paths().source.patterns, '**', '*.json'),
        // normalizePath(paths().source.patterns, '**', '*.md'),
        // normalizePath(paths().source.data, '**', '*.json'),
        normalizePath(paths().source.css, '**/*.scss'),
        // normalizePath(paths().source.fonts, '**', '*'),
        // normalizePath(paths().source.images, '**', '*'),
        // normalizePath(paths().source.icons, '**', '*'),
        // normalizePath(paths().source.js, '**', '*'),
        // normalizePath(paths().source.meta, '**', '*'),
        // normalizePath(paths().source.annotations, '**', '*')
      ].concat(getTemplateWatches()),
      config: { awaitWriteFinish: true },
      tasks: gulp.series("pl-sass", reload)
    }
  ];

  watchers.forEach(watcher => {
    console.log('\n' + 'Watching ' + watcher.name + ':');
    watcher.paths.forEach(p => console.log('  ' + p));
    gulp.watch(watcher.paths, watcher.config, watcher.tasks);
  });
  console.log();
}

gulp.task('pl-sass', function(){
  return gulp.src(path.resolve(paths().source.css, '**/*.scss'))
    .pipe(sass().on('error', sass.logError))
    .pipe(gulp.dest(path.resolve(paths().public.css)));
});

gulp.task('patternlab:watch', gulp.series(watch));
gulp.task('default', gulp.series('pl-sass'));
//gulp.task('default', gulp.series('patternlab:build'));
