<style type="text/css">

.balloon {
    width: 75%;
    position: relative;
}

.on-left {
    margin-left: 0;
}

.on-right {
    margin-left: 25%;
}

.balloon .balloon-content {
    position: relative;
    padding: 4mm;
    padding: 4mm;
    margin-left: 2.8mm;
    margin-right: 2.8mm;
    margin-bottom: 4mm;
    border:  0.1mm solid grey;
    border-top: none;
    border-radius: 2mm;
    text-align: left;
    color:   black;
}

.balloon .balloon-corner {
    width: 0;
    height: 0;
    border: none;
    margin: 0;
    padding: 0;
    position: absolute;
    top: 0;
}

.on-left .balloon-content {
    background-color: #FEFEFE;
    border-top-left-radius: 0;
}

.on-right .balloon-content {
    background-color: #E2FECB;
    border-top-right-radius: 0;
}

.on-left .balloon-corner {
    border-top:  solid 3mm #FEFEFE;
    border-left: solid 3mm transparent;
    left: 0;
}

.on-right .balloon-corner {
    border-top:   solid 3mm #E2FECB;
    border-right: solid 3mm transparent;
    right: 0;
}

.balloon .balloon-status {
    color: grey;
    position: absolute;
    right: 2mm;
    bottom: 2mm;
    font-size: 80%
}

.balloon .balloon-status img {
    width: 3mm;
}

</style>
<page backcolor="#DDD">
    <div class="balloon on-right">
        <div class="balloon-content">
            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam lacinia lectus ut lobortis vestibulum. In id consectetur enim. Donec eu erat ut magna consectetur vestibulum ac malesuada turpis. Suspendisse turpis risus, feugiat id gravida eu, euismod id orci. Nunc egestas ut erat molestie rutrum. Morbi posuere posuere sapien sed mattis. Fusce dapibus nunc leo, viverra mollis massa varius non. Ut in maximus quam. Aliquam volutpat consectetur odio, sed viverra eros tincidunt non. Ut consequat mi maximus congue faucibus. Morbi id finibus nisi.
            <div class="balloon-status">10:01 PM <img src="./res/check.png" alt="check" /></div>
        </div>
        <div class="balloon-corner"></div>
    </div>
    <div class="balloon on-left">
        <div class="balloon-content">
            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam lacinia lectus ut lobortis vestibulum. In id consectetur enim. Donec eu erat ut magna consectetur vestibulum ac malesuada turpis. Suspendisse turpis risus, feugiat id gravida eu, euismod id orci. Nunc egestas ut erat molestie rutrum. Morbi posuere posuere sapien sed mattis. Fusce dapibus nunc leo, viverra mollis massa varius non. Ut in maximus quam. Aliquam volutpat consectetur odio, sed viverra eros tincidunt non. Ut consequat mi maximus congue faucibus. Morbi id finibus nisi.
            <div class="balloon-status">10:02 PM <img src="./res/check.png" alt="check" /></div>
        </div>
        <div class="balloon-corner"></div>
    </div>
    <div class="balloon on-right">
        <div class="balloon-content">
            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam lacinia lectus ut lobortis vestibulum.
            <div class="balloon-status">10:03 PM</div>
        </div>
        <div class="balloon-corner"></div>
    </div>
    <div class="balloon on-left">
        <div class="balloon-content">
            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam lacinia lectus ut lobortis vestibulum.
            <div class="balloon-status">10:04 PM</div>
        </div>
        <div class="balloon-corner"></div>
    </div>
</page>