console.log('helloWorld');




const slides = document.querySelectorAll('.list .container');
let currentSlide = 0;

function showSlide(index) {
    slides.forEach(slide => {
        slide.classList.remove('active');
        slide.offsetWidth;
    });

    slides[index].classList.add('active');
}


const next = document.querySelector('.carouselBtn button:nth-child(2)');

if (next) {

    next.addEventListener('click', () => {
        currentSlide++;
        if (currentSlide >= slides.length) {
            currentSlide = 0;
        }

        showSlide(currentSlide);
    })

}

const prev = document.querySelector('.carouselBtn button:nth-child(1)');
if (prev) {
    prev.addEventListener('click', () => {
        currentSlide--;
        if (currentSlide < 0) {
            currentSlide = slides.length - 1;
        }
        showSlide(currentSlide);
    });
}



function showAboutInfo(content, name) {

    const info = document.querySelectorAll('.info');


    info.forEach(infoContainer => {
        infoContainer.classList.remove('active');
        infoContainer.offsetWidth;
    });

    const profiles = document.querySelectorAll('.profile');
    profiles.forEach(profile => {
        profile.classList.remove('active');
        profile.offsetWidth;
    })

    document.getElementById(content).classList.add('active');
    document.getElementById(name).classList.add('active');


}

const prevStage = document.getElementById('prevStage');
const nextStage = document.getElementById('nextStage');
const stages = document.querySelectorAll('#bookingForm fieldset');
const submitBooking = document.getElementById('submitForm');
let stagesIndex = 0;



function showCurrentForm(index) {
    stages.forEach(stage => {
        stage.classList.remove('active');
        stage.offsetWidth;
    })

    stages[index].classList.add('active');
    updateProgressBar(index);
}

if (nextStage) {



    nextStage.addEventListener('click', () => {
        stagesIndex++;

        if (stagesIndex === stages.length - 1) {
            nextStage.disabled = true;
            nextStage.classList.remove('bg-dark');
            nextStage.classList.remove('text-light');
            submitBooking.disabled = false;
        }
        prevStage.disabled = false;
        prevStage.classList.add('bg-dark');
        prevStage.classList.add('text-light');
        showCurrentForm(stagesIndex);
    })

}

if (prevStage) {
    prevStage.addEventListener('click', () => {
        stagesIndex--;

        if (stagesIndex === 0) {
            prevStage.disabled = true;
            prevStage.classList.remove('bg-dark');
            prevStage.classList.remove('text-light');

        }
        submitBooking.disabled = true;
        nextStage.disabled = false;
        nextStage.classList.add('bg-dark');
        nextStage.classList.add('text-light');
        showCurrentForm(stagesIndex);
    })
}

function updateProgressBar(index) {
    const totalStep = stages.length;
    const progressPercent = ((index + 1) / totalStep) * 100

    const progressBar = document.getElementById('progress-bar');

    const progressContainer = document.getElementById('progress');

    if (progressBar) {
        progressBar.style.width = progressPercent + '%';
    }

    if (progressContainer) {
        progressContainer.setAttribute('aria-valuenow', progressPercent);
    }
}


// FETCH INCLUSION AND ADDONS USING RADIO BUTTON


const radios = document.querySelectorAll('.radio');
const lists = document.querySelectorAll('#inclusionList');


if (radios.length > 0) {
    radios.forEach(radio => {
        radio.addEventListener('change', function () {
            if (this.checked) {
                const packageId = this.value;
                console.log(packageId);
                loadPackageDetails(packageId);
            }
        })
    })
}

function loadPackageDetails(packageId) {
    console.log('loading package details for: ' + packageId);

    fetch(`booking.php?packageId=${packageId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('network response not okay');
            }
            return response.json();
        })
        .then(data => {
            console.log('data received: ', data);
            displayPackageDetails(data);
        })

        .catch(error => {
            console.log('Error: ', error);
        });

}

function displayPackageDetails(data) {
    console.log("Inclusion", data.inclusions);
    console.log("Addons", data.addons);

    const allInclusionList = document.querySelectorAll('.inclusionList');
    allInclusionList.forEach(list => {
        list.innerHTML = '';
    })
    const radiosChecked = document.querySelector('.radio:checked');

    if (radiosChecked) {

        const label = radiosChecked.nextElementSibling;
        const inclusionList = label.querySelector('.inclusionList');

        if (inclusionList && data.inclusions) {
            inclusionList.innerHTML = '';

            if (data.inclusions.length > 0) {
                data.inclusions.forEach(inclusion => {
                    const li = document.createElement('li');
                    li.textContent = inclusion;
                    li.classList.add('text-muted');
                    inclusionList.appendChild(li);

                    displayAddOns(data);
                })
            } else {
                inclusionList.innerHTML = '<li class="text-muted">No inclusions available</li>';
            }
        }


    }

}







function displayAddOns(data) {


    const allAddOnsList = document.querySelectorAll('.addons');
    allAddOnsList.forEach(list => {
        list.innerHTML = '';
    })


    const addOnsList = document.querySelector('.addons');

    if (data.addons && addOnsList) {
        addOnsList.innerHTML = ' ';

        if (data.addons.length > 0) {
            data.addons.forEach(addon => {
                const formCheckDiv = document.createElement('div');
                const checkInput = document.createElement('input');
                checkInput.setAttribute('type', 'checkbox')
                const labelCheck = document.createElement('label');

                labelCheck.textContent = addon.Description + ' - ' + addon.Price;

                checkInput.classList.add('form-check-input');
                labelCheck.classList.add('form-check-label');
                checkInput.classList.add('checkInput');
                labelCheck.classList.add('checkInputLabel');
                labelCheck.classList.add('serif');
                checkInput.classList.add('me-2');
                formCheckDiv.classList.add('d-flex');
                formCheckDiv.classList.add('align-items-start');
                formCheckDiv.classList.add('justify-content-center');


                formCheckDiv.appendChild(checkInput);
                formCheckDiv.appendChild(labelCheck);

                addOnsList.appendChild(formCheckDiv);

                console.log("Addons asjdakjdk", addon.Description);


            })
        } else {
            addOnsList.innerHTML = '<li class="text-muted">No inclusions available</li>';
        }
    }

}


const contactInput = document.getElementById("contactInput");
const profileSubmitBtn = document.getElementById("profileSubmitBtn");

if (contactInput) {
    contactInput.addEventListener('keyup', () => {

    let inputLength = contactInput.value.length;

    if(inputLength === 11){
        profileSubmitBtn.disabled = false;
    }else{
        profileSubmitBtn.disabled = true;
    }
    })
}

