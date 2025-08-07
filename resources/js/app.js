import './bootstrap';
import Uppy from '@uppy/core';
import Dashboard from '@uppy/dashboard';
import XHRUpload from '@uppy/xhr-upload';
import '@uppy/core/dist/style.css';
import '@uppy/dashboard/dist/style.css';

window.initUppyVideoUploader = function(uploadUrl) {
    const uppy = new Uppy({
        restrictions: {
            maxNumberOfFiles: 1,
            allowedFileTypes: ['video/*']
        },
        autoProceed: true
    })
    .use(Dashboard, {
        inline: true,
        target: '#uppy-video-uploader',
        note: 'فقط فيديو واحد، الحجم غير محدود (chunked upload)'
    })
    .use(XHRUpload, {
        endpoint: uploadUrl,
        fieldName: 'video',
        formData: true,
        bundle: false
    });

    uppy.on('complete', (result) => {
        if(result.successful.length > 0) {
            const videoPath = result.successful[0].response.body.path;
            document.getElementById('uploaded_video_path').value = videoPath;
        }
    });
}
