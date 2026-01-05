
    @foreach($questions as $question)
        <div class="card mb-3">
            <div class="card-body">

                <h6 class="fw-bold">{{ $question->question }}</h6>

                <div class="mb-2">
                    <label class="me-3">
                        <input type="radio"
                               name="questions[{{ $question->id }}][answer]"
                               value="yes"> Yes
                    </label>

                    <label>
                        <input type="radio"
                               name="questions[{{ $question->id }}][answer]"
                               value="no"> No
                    </label>
                </div>

                @if($question->require_remark)
                    <textarea
                        name="questions[{{ $question->id }}][remark]"
                        class="form-control mt-2"
                        rows="2"
                        placeholder="Enter remark if Yes">
                    </textarea>
                @endif

            </div>
        </div>
    @endforeach

