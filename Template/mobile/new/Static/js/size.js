(function (win,doc) {
    function setSize() {
        doc.documentElement.style.fontSize='16px';
    }
    setSize();
    win.addEventListener('resize',setSize,false)
})(window,document)
