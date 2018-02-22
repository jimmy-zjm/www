// 引入gulp库
var gulp = require('gulp'),  // 基础库
    browserSync = require('browser-sync'),  // 监听浏览器自动刷新
    less = require('gulp-less'),  // 编译less
    clean = require('gulp-clean'),  // 清理目录
    contentInclude = require('gulp-content-includer'),  // 合并html模块
    miniCss = require('gulp-clean-css'),  // css压缩
    miniImg = require('gulp-imagemin'),  // img压缩
    cache = require('gulp-cache'),
    rename = require('gulp-rename'),  // 文件重命名
     miniJs = require('gulp-uglify'), //js压缩
    concat = require("gulp-concat"), //文件合并
    autoprefixer = require("gulp-autoprefixer"),//添加兼容
    csscomb = require('gulp-csscomb'),//css排列
    notify = require("gulp-notify"),  //less编译错误通知
    plumber = require("gulp-plumber"), //less编译错误通知
    changed = require('gulp-changed');//文件比较只有改变的才走管道

// 清理目录
gulp.task('clean', function() {
    return gulp.src('dist/*', { read: false })
    .pipe(clean())
});

// 合并html模块
gulp.task('process-html', function() {
    return gulp.src(['src/*.html', 'src/**/*.html'], { base: 'src' })
        // .pipe(changed('dist/')) //导致一个问题：tpl内容改变不会发布到dist
        .pipe(contentInclude({
            includerReg: /<!\-\-include\s+"([^"]+)"\-\->/g
        }))

        .pipe(gulp.dest('dist/'));
    });

//编译less文件
gulp.task('process-less', function() {
    return gulp.src(['src/less/*.less','!src/less/base.less'])

    .pipe(plumber({errorHandler: notify.onError('Error: <%= error.message %>')}))
    .pipe(less())
    .pipe(autoprefixer({
        browsers: ['last 4 versions'],
            cascade: true, //是否美化属性值 默认：true 像这样：
            //-webkit-transform: rotate(45deg);
            //        transform: rotate(45deg);
            remove:true //是否去掉不必要的前缀 默认：true 
        }))
        .pipe(csscomb())      //css按照良好的顺序排列

     .pipe(changed('src/css/'))
      
      .pipe(gulp.dest('src/css/'));
  })

//css文件合并及重命名
gulp.task('process-css', function() {
	return gulp.src('src/css/*.css')
    .pipe(changed('dist/css/')) 
		// .pipe(concat('style.css'))
		// .pipe(rename({ suffix: '.min' }))
        .pipe(miniCss())   //压缩css 
		.pipe(gulp.dest('dist/css/'));
    });

//拷贝一部分文件到发布环境
gulp.task('process-js', function() {
    return gulp.src('src/js/*')
    .pipe(changed('dist/js/'))
    .pipe(miniJs())
    .pipe(gulp.dest('dist/js/'));
});

//拷贝一部分文件到发布环境
gulp.task('process-img', function() {
    return gulp.src(['src/img/**/*','src/img/*'], { base: 'src/img' })
    .pipe(cache(miniImg({
         optimizationLevel:7, //类型：Number  默认：3  取值范围：0-7（优化等级）
         progressive: true, //类型：Boolean 默认：false 无损压缩jpg图片
    })))
    .pipe(changed('dist/img/'))

    .pipe(gulp.dest('dist/img/'));
});
//拷贝一部分文件到发布环境
gulp.task('process-font', function() {
    return gulp.src('src/fonts/*')
    .pipe(changed('dist/fonts/'))
    .pipe(gulp.dest('dist/fonts/'));
});

//自动执行部分,检测
gulp.task('auto', function() {
    gulp.watch('src/less/*.less', ['process-less']);
    gulp.watch('src/css/*.css', ['process-css']);
    gulp.watch('src/js/*.js', ['process-js']);
    gulp.watch(['src/img/**/*','src/img/*'], ['process-img']);
    gulp.watch('src/fonts/*', ['process-font']);
    gulp.watch(['src/*.html', 'src/**/*.html', 'src/tpl/*.tpl'], ['process-html']);
})

// 开启静态文件服务器
gulp.task('browser-sync', function() {
    var files = [
    'dist/*.html',
    'dist/**/.html',
    'dist/img/*',
    'dist/fonts/*',
    'dist/img/**/*',
    'dist/css/*',
    'dist/css/**/*',
    'dist/js/*'
    ];
    browserSync.init(files, {
        server: {
            baseDir: "./dist"
        }
    });
});

// 默认任务
// 're-name',
gulp.task('default', ['clean'], function() {
    gulp.start('process-html', 'process-less','process-css','process-js', 'process-img','process-font', 'auto', 'browser-sync')
});
