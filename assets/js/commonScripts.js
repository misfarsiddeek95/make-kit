function run_waitMe(el){
    $(el).waitMe({
      effect: 'facebook',
      text: 'Please wait...',
      bg: 'rgba(255,255,255,0.7)',
      color: '#000',
      maxSize: '',
      source: '../assets/img/img.svg',
      onClose: function() {}
    });
}