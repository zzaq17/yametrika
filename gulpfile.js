const gulp = require('gulp')
const {src, dest, watch, parallel, series } = require('gulp')
const plumber = require('gulp-plumber')
const remoteSynchronization = require('gulp-rsync')

function rs() {
    return gulp.src('site/**')
				.pipe(plumber())
        .pipe(remoteSynchronization({
            root: '/**',
            hostname: 'yourLogin@yourIp',
            destination: 'sitePath',
            port: 25212,
            include: ['*.htaccess'],
            exclude: ['**/Thumbs.db', '**/*.DS_Store', ],
            recursive: true,
            archive: true,
            silent: false,
            compress: true
    }))
}


exports.deploy = series(
    rs
);